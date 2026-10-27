<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Payment;
use App\Models\StockDelivery;
use App\Models\Supplier;
use App\Services\LedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SupplierLedgerController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Supplier Ledgers List View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = Supplier::select('suppliers.*');

            return DataTables::of($suppliers)
                ->addColumn('balance', function ($row) {
                    $bal = $this->ledgerService->getSupplierBalance($row->id);
                    $badgeClass = ($bal > 0) ? 'label label-danger arrowed' : (($bal < 0) ? 'label label-info arrowed' : 'label label-success arrowed');
                    return '<span class="' . $badgeClass . '">PKR ' . number_format($bal, 2) . '</span>';
                })
                ->addColumn('total_purchased', function ($row) {
                    $purchased = (float) StockDelivery::where('supplier_id', $row->id)
                        ->where(function ($q) {
                            $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
                        })
                        ->sum('total_amount');
                    return 'PKR ' . number_format($purchased, 2);
                })
                ->addColumn('total_paid', function ($row) {
                    $cashPaid = (float) StockDelivery::where('supplier_id', $row->id)
                        ->where(function ($q) {
                            $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
                        })
                        ->sum('paid_amount');
                    $voucherPaid = (float) Payment::where('party_type', Payment::PARTY_SUPPLIER)
                        ->where('party_id', $row->id)
                        ->sum('amount');
                    return 'PKR ' . number_format($cashPaid + $voucherPaid, 2);
                })
                ->addColumn('action', function ($row) {
                    $statementUrl = route('supplier.ledger.show', $row->id);
                    $bal = $this->ledgerService->getSupplierBalance($row->id);
                    $html = '<div class="btn-group">';
                    $html .= '<a href="' . $statementUrl . '" class="btn btn-xs btn-info" title="View Statement"><i class="fa fa-book"></i> Statement</a> ';
                    $html .= '<button type="button" class="btn btn-xs btn-warning" onclick="openSupplierPaymentModal(' . $row->id . ', \'' . addslashes($row->name) . '\', ' . $bal . ')" title="Record Payment"><i class="fa fa-credit-card"></i> Pay Supplier</button>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['balance', 'action'])
                ->make(true);
        }

        // Global Supplier summary metrics across all suppliers
        $totalPurchased = (float) StockDelivery::whereNotNull('supplier_id')
            ->where(function ($q) {
                $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
            })
            ->sum('total_amount');

        $totalCashPaid = (float) StockDelivery::whereNotNull('supplier_id')
            ->where(function ($q) {
                $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
            })
            ->sum('paid_amount');

        $totalVouchers = (float) Payment::where('party_type', Payment::PARTY_SUPPLIER)->sum('amount');

        $totalPaid = round($totalCashPaid + $totalVouchers, 2);
        $totalPayable = round($totalPurchased - $totalPaid, 2);

        $supplierCount = Supplier::count();

        return view('Ledger.supplier_index', [
            'totalPurchased' => $totalPurchased,
            'totalPaid' => $totalPaid,
            'totalPayable' => $totalPayable,
            'supplierCount' => $supplierCount,
        ]);
    }

    /**
     * Supplier Detailed Statement View
     */
    public function show($id, Request $request)
    {
        $supplier = Supplier::findOrFail($id);
        $account = $this->ledgerService->getOrCreateAccount(Account::HOLDER_SUPPLIER, $supplier->id);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $startSql = $startDate ? Carbon::parse($startDate)->toDateString() : null;
        $endSql = $endDate ? Carbon::parse($endDate)->toDateString() : null;

        $statement = $this->ledgerService->getSupplierStatement($supplier, $startSql, $endSql);

        $lifetimePurchased = $statement['lifetime_purchased'];
        $lifetimeCashPaid = $statement['lifetime_cash_paid'];
        $lifetimeCreditPurchases = $statement['lifetime_credit_purchases'];
        $lifetimeVoucherPaid = $statement['lifetime_voucher_paid'];
        $lifetimePaid = $statement['lifetime_total_paid'];
        $currentBalance = $statement['current_balance'];

        if ($request->get('export') === 'csv') {
            return $this->exportStatementCsv($supplier, $statement, $startDate, $endDate);
        }

        return view('Ledger.supplier_statement', [
            'supplier' => $supplier,
            'account' => $account,
            'statement' => $statement,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'lifetimePurchased' => $lifetimePurchased,
            'lifetimeCashPaid' => $lifetimeCashPaid,
            'lifetimeCreditPurchases' => $lifetimeCreditPurchases,
            'lifetimeVoucherPaid' => $lifetimeVoucherPaid,
            'lifetimePaid' => $lifetimePaid,
            'currentBalance' => $currentBalance,
            'isPrint' => $request->get('view') === 'print',
        ]);
    }

    /**
     * Get Supplier Current Balance (AJAX)
     */
    public function getBalance($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['status' => false, 'balance' => 0.00, 'message' => 'Supplier not found'], 404);
        }

        $balance = $this->ledgerService->getSupplierBalance($supplier->id);

        return response()->json([
            'status' => true,
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'phone' => $supplier->phone,
            'balance' => $balance,
            'formatted_balance' => 'PKR ' . number_format($balance, 2),
        ]);
    }

    /**
     * Record Supplier Settlement / Payment Payout Voucher
     */
    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer',
            'payment_date' => 'required',
        ]);

        $supplier = Supplier::findOrFail($id);

        $date = Carbon::parse($request->get('payment_date'))->toDateString();

        try {
            $payment = $this->ledgerService->recordPayment([
                'party_type' => Payment::PARTY_SUPPLIER,
                'party_id' => $supplier->id,
                'amount' => (float) $request->get('amount'),
                'payment_method' => $request->get('payment_method'),
                'payment_date' => $date,
                'reference_no' => $request->get('reference_no'),
                'notes' => $request->get('notes'),
                'shop_id' => auth()->check() ? auth()->user()->shop_id : null,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment recorded successfully! Voucher: ' . $payment->voucher_no,
                    'data' => $payment,
                    'voucher_url' => route('supplier.ledger.voucher', $payment->id),
                ]);
            }

            return redirect()->back()->with('success', 'Payment recorded successfully! Voucher: ' . $payment->voucher_no);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print Payment Voucher
     */
    public function printVoucher($paymentId)
    {
        $payment = Payment::with(['supplier', 'creator', 'shop'])->findOrFail($paymentId);

        return view('Ledger.voucher_print', [
            'payment' => $payment,
            'party' => $payment->supplier,
            'partyType' => 'Supplier',
            'voucherTitle' => 'PAYMENT VOUCHER',
        ]);
    }

    /**
     * Export Statement to CSV
     */
    protected function exportStatementCsv(Supplier $supplier, array $statement, ?string $startDate = null, ?string $endDate = null)
    {
        $filename = 'supplier_statement_' . $supplier->id . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($supplier, $statement, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['SUPPLIER LEDGER STATEMENT']);
            fputcsv($file, ['Supplier Name:', $supplier->name]);
            fputcsv($file, ['Phone:', $supplier->phone ?? 'N/A']);
            fputcsv($file, ['Statement Generated At:', date('d-m-Y H:i')]);
            if ($startDate || $endDate) {
                fputcsv($file, ['Filter Period:', ($startDate ? date('d-m-Y', strtotime($startDate)) : 'Start') . ' to ' . ($endDate ? date('d-m-Y', strtotime($endDate)) : 'Present')]);
            }
            fputcsv($file, []);

            fputcsv($file, [
                'Date',
                'Invoice / Reference No',
                'Transaction Type',
                'Payment Method',
                'Description',
                'Total Purchase Amount (PKR)',
                'Amount Paid (PKR)',
                'Credit / Due Impact (PKR)',
                'Running Outstanding Balance (PKR)'
            ]);

            fputcsv($file, [
                $startDate ? date('d-m-Y', strtotime($startDate)) : '-',
                'OPENING',
                'Opening Balance',
                '-',
                'Opening Balance as of ' . ($startDate ? date('d-m-Y', strtotime($startDate)) : 'Inception'),
                '-',
                '-',
                '-',
                number_format($statement['opening_balance'], 2, '.', '')
            ]);

            foreach ($statement['rows'] as $row) {
                fputcsv($file, [
                    $row['date'],
                    $row['invoice_no'],
                    $row['type_label'],
                    $row['payment_method'],
                    $row['description'],
                    $row['total_amount'] > 0 ? number_format($row['total_amount'], 2, '.', '') : '0.00',
                    $row['paid_amount'] > 0 ? number_format($row['paid_amount'], 2, '.', '') : '0.00',
                    $row['due_amount'] > 0 ? number_format($row['due_amount'], 2, '.', '') : '0.00',
                    number_format($row['running_balance'], 2, '.', ''),
                ]);
            }

            fputcsv($file, [
                'PERIOD TOTALS',
                '',
                '',
                '',
                '',
                number_format($statement['period_total_purchased'], 2, '.', ''),
                number_format($statement['period_total_paid'], 2, '.', ''),
                number_format($statement['period_total_credit_purchases'], 2, '.', ''),
                number_format($statement['closing_balance'], 2, '.', ''),
            ]);

            fputcsv($file, []);
            fputcsv($file, ['LIFETIME ACCOUNT RECONCILIATION SUMMARY']);
            fputcsv($file, ['Total Purchases (Cash + Credit):', number_format($statement['lifetime_purchased'], 2, '.', '')]);
            fputcsv($file, ['Total Cash Paid at Purchase:', number_format($statement['lifetime_cash_paid'], 2, '.', '')]);
            fputcsv($file, ['Total Credit Purchases:', number_format($statement['lifetime_credit_purchases'], 2, '.', '')]);
            fputcsv($file, ['Total Voucher Payments:', number_format($statement['lifetime_voucher_paid'], 2, '.', '')]);
            fputcsv($file, ['Total Amount Paid:', number_format($statement['lifetime_total_paid'], 2, '.', '')]);
            fputcsv($file, ['Current Outstanding Balance (Payable):', number_format($statement['current_balance'], 2, '.', '')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
