<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SaleOrder;
use App\Services\LedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CustomerLedgerController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Customer Ledgers List View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::select('customers.*');

            return DataTables::of($customers)
                ->addColumn('balance', function ($row) {
                    $bal = $this->ledgerService->getCustomerBalance($row->id);
                    $badgeClass = ($bal > 0) ? 'label label-danger arrowed' : (($bal < 0) ? 'label label-info arrowed' : 'label label-success arrowed');
                    return '<span class="' . $badgeClass . '">PKR ' . number_format($bal, 2) . '</span>';
                })
                ->addColumn('total_billed', function ($row) {
                    $billed = (float) SaleOrder::where('customer_id', $row->id)
                        ->selectRaw('COALESCE(SUM(COALESCE(total_amount, total_price, 0)), 0) as total')
                        ->value('total');
                    return 'PKR ' . number_format($billed, 2);
                })
                ->addColumn('total_paid', function ($row) {
                    $posPaid = (float) SaleOrder::where('customer_id', $row->id)->sum('paid_amount');
                    $voucherPaid = (float) Payment::where('party_type', Payment::PARTY_CUSTOMER)
                        ->where('party_id', $row->id)
                        ->sum('amount');
                    return 'PKR ' . number_format($posPaid + $voucherPaid, 2);
                })
                ->addColumn('action', function ($row) {
                    $statementUrl = route('customer.ledger.show', $row->id);
                    $reportUrl = route('report.customer_sales', ['customer_id' => $row->id]);
                    $bal = $this->ledgerService->getCustomerBalance($row->id);
                    $html = '<div class="btn-group">';
                    $html .= '<a href="' . $statementUrl . '" class="btn btn-xs btn-info" title="View Statement"><i class="fa fa-book"></i> Statement</a> ';
                    $html .= '<a href="' . $reportUrl . '" class="btn btn-xs btn-purple" title="Product Sales Report"><i class="fa fa-list-alt"></i> Sales Report</a> ';
                    $html .= '<button type="button" class="btn btn-xs btn-success" onclick="openCustomerPaymentModal(' . $row->id . ', \'' . addslashes($row->name) . '\', ' . $bal . ')" title="Collect Due"><i class="fa fa-money"></i> Collect Due</button>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['balance', 'action'])
                ->make(true);
        }

        // Global Customer summary metrics
        $totalBilled = (float) SaleOrder::whereNotNull('customer_id')
            ->where('customer_id', '>', 0)
            ->selectRaw('COALESCE(SUM(COALESCE(total_amount, total_price, 0)), 0) as total')
            ->value('total');

        $totalPosPaid = (float) SaleOrder::whereNotNull('customer_id')
            ->where('customer_id', '>', 0)
            ->sum('paid_amount');

        $totalVouchers = (float) Payment::where('party_type', Payment::PARTY_CUSTOMER)->sum('amount');

        $totalPaid = round($totalPosPaid + $totalVouchers, 2);
        $totalReceivable = round($totalBilled - $totalPaid, 2);

        $customerCount = Customer::count();

        return view('Ledger.customer_index', [
            'totalBilled' => $totalBilled,
            'totalPaid' => $totalPaid,
            'totalReceivable' => $totalReceivable,
            'customerCount' => $customerCount,
        ]);
    }

    /**
     * Customer Detailed Statement View
     */
    public function show($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        $account = $this->ledgerService->getOrCreateAccount(Account::HOLDER_CUSTOMER, $customer->id);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Format dates if provided (e.g. from d-m-Y to Y-m-d)
        $startSql = $startDate ? Carbon::parse($startDate)->toDateString() : null;
        $endSql = $endDate ? Carbon::parse($endDate)->toDateString() : null;

        $statement = $this->ledgerService->getCustomerStatement($customer, $startSql, $endSql);

        $lifetimeSales = $statement['lifetime_sales'];
        $lifetimeCashReceived = $statement['lifetime_cash_received'];
        $lifetimeOnlineReceived = $statement['lifetime_online_received'];
        $lifetimeCreditSales = $statement['lifetime_credit_sales'];
        $lifetimeTotalCollected = $statement['lifetime_total_collected'];
        $currentBalance = $statement['current_balance'];

        // Handle Export to CSV
        if ($request->get('export') === 'csv') {
            return $this->exportStatementCsv($customer, $statement, $startDate, $endDate);
        }

        return view('Ledger.customer_statement', [
            'customer' => $customer,
            'account' => $account,
            'statement' => $statement,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'lifetimeSales' => $lifetimeSales,
            'lifetimeCashReceived' => $lifetimeCashReceived,
            'lifetimeOnlineReceived' => $lifetimeOnlineReceived,
            'lifetimeCreditSales' => $lifetimeCreditSales,
            'lifetimeTotalCollected' => $lifetimeTotalCollected,
            'currentBalance' => $currentBalance,
            'isPrint' => $request->get('view') === 'print',
        ]);
    }

    /**
     * Get Customer Current Balance (AJAX)
     */
    public function getBalance($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['status' => false, 'balance' => 0.00, 'message' => 'Customer not found'], 404);
        }

        $balance = $this->ledgerService->getCustomerBalance($customer->id);

        return response()->json([
            'status' => true,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'mobile' => $customer->mobile,
            'balance' => $balance,
            'formatted_balance' => 'PKR ' . number_format($balance, 2),
        ]);
    }

    /**
     * Record Customer Settlement / Collection Voucher
     */
    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer',
            'payment_date' => 'required',
        ]);

        $customer = Customer::findOrFail($id);

        $date = Carbon::parse($request->get('payment_date'))->toDateString();

        try {
            $payment = $this->ledgerService->recordPayment([
                'party_type' => Payment::PARTY_CUSTOMER,
                'party_id' => $customer->id,
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
                    'voucher_url' => route('customer.ledger.voucher', $payment->id),
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
     * Print Payment / Receipt Voucher
     */
    public function printVoucher($paymentId)
    {
        $payment = Payment::with(['customer', 'creator', 'shop'])->findOrFail($paymentId);

        return view('Ledger.voucher_print', [
            'payment' => $payment,
            'party' => $payment->customer,
            'partyType' => 'Customer',
            'voucherTitle' => 'RECEIPT VOUCHER',
        ]);
    }

    /**
     * Export Statement to CSV
     */
    protected function exportStatementCsv(Customer $customer, array $statement, ?string $startDate = null, ?string $endDate = null)
    {
        $filename = 'customer_statement_' . $customer->id . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($customer, $statement, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // Header info
            fputcsv($file, ['CUSTOMER ACCOUNT STATEMENT']);
            fputcsv($file, ['Customer Name:', $customer->name]);
            fputcsv($file, ['Mobile:', $customer->mobile ?? 'N/A']);
            fputcsv($file, ['CNIC:', $customer->cnic ?? 'N/A']);
            fputcsv($file, ['Address:', $customer->address ?? 'N/A']);
            fputcsv($file, ['Statement Generated At:', date('d-m-Y H:i')]);
            if ($startDate || $endDate) {
                fputcsv($file, ['Filter Period:', ($startDate ? date('d-m-Y', strtotime($startDate)) : 'Start') . ' to ' . ($endDate ? date('d-m-Y', strtotime($endDate)) : 'Present')]);
            }
            fputcsv($file, []);

            // Table headers
            fputcsv($file, [
                'Date',
                'Invoice / Reference No',
                'Transaction Type',
                'Payment Method',
                'Description',
                'Total Sale Amount (PKR)',
                'Amount Paid (PKR)',
                'Credit / Due Impact (PKR)',
                'Running Outstanding Balance (PKR)'
            ]);

            // Opening Balance row
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

            // Data rows
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

            // Period Totals row
            fputcsv($file, [
                'PERIOD TOTALS',
                '',
                '',
                '',
                '',
                number_format($statement['period_total_sales'], 2, '.', ''),
                number_format($statement['period_total_collected'], 2, '.', ''),
                number_format($statement['period_credit_sales'], 2, '.', ''),
                number_format($statement['closing_balance'], 2, '.', ''),
            ]);

            fputcsv($file, []);
            fputcsv($file, ['LIFETIME ACCOUNT RECONCILIATION SUMMARY']);
            fputcsv($file, ['Total Sales Amount (Cash, Credit & Online):', number_format($statement['lifetime_sales'], 2, '.', '')]);
            fputcsv($file, ['Total Cash Received:', number_format($statement['lifetime_cash_received'], 2, '.', '')]);
            fputcsv($file, ['Total Online Payments Received:', number_format($statement['lifetime_online_received'], 2, '.', '')]);
            fputcsv($file, ['Total Credit Sales:', number_format($statement['lifetime_credit_sales'], 2, '.', '')]);
            fputcsv($file, ['Total Amount Collected:', number_format($statement['lifetime_total_collected'], 2, '.', '')]);
            fputcsv($file, ['Remaining Outstanding Balance (Receivable):', number_format($statement['current_balance'], 2, '.', '')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
