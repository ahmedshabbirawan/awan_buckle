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
                    $account = $row->account;
                    if ($account) {
                        $billed = (float) $account->entries()->sum('debit');
                    } else {
                        $billed = (float) SaleOrder::where('customer_id', $row->id)->sum('total_amount');
                    }
                    return 'PKR ' . number_format($billed, 2);
                })
                ->addColumn('total_paid', function ($row) {
                    $account = $row->account;
                    if ($account) {
                        $paid = (float) $account->entries()->sum('credit');
                    } else {
                        $paid = (float) SaleOrder::where('customer_id', $row->id)->sum('paid_amount');
                    }
                    return 'PKR ' . number_format($paid, 2);
                })
                ->addColumn('action', function ($row) {
                    $statementUrl = route('customer.ledger.show', $row->id);
                    $bal = $this->ledgerService->getCustomerBalance($row->id);
                    $html = '<div class="btn-group">';
                    $html .= '<a href="' . $statementUrl . '" class="btn btn-xs btn-info" title="View Statement"><i class="fa fa-book"></i> Statement</a> ';
                    $html .= '<button type="button" class="btn btn-xs btn-success" onclick="openCustomerPaymentModal(' . $row->id . ', \'' . addslashes($row->name) . '\', ' . $bal . ')" title="Collect Due"><i class="fa fa-money"></i> Collect Due</button>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['balance', 'action'])
                ->make(true);
        }

        // Global Customer summary metrics
        $customerAccountIds = Account::where('holder_type', Account::HOLDER_CUSTOMER)->pluck('id');
        
        $totals = DB::table('ledger_entries')
            ->whereIn('account_id', $customerAccountIds)
            ->selectRaw('COALESCE(SUM(debit), 0) as total_billed, COALESCE(SUM(credit), 0) as total_paid')
            ->first();

        $totalBilled = $totals ? (float) $totals->total_billed : 0.00;
        $totalPaid = $totals ? (float) $totals->total_paid : 0.00;
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

        $statement = $this->ledgerService->getAccountStatement($account, $startSql, $endSql);

        // Calculate summary figures
        $allTotals = $account->entries()
            ->selectRaw('COALESCE(SUM(debit), 0) as total_billed, COALESCE(SUM(credit), 0) as total_paid')
            ->first();

        $lifetimeBilled = (float) ($allTotals->total_billed ?? 0);
        $lifetimePaid = (float) ($allTotals->total_paid ?? 0);
        $currentBalance = round($lifetimeBilled - $lifetimePaid, 2);

        // Handle Export to CSV
        if ($request->get('export') === 'csv') {
            return $this->exportStatementCsv($customer, $statement);
        }

        return view('Ledger.customer_statement', [
            'customer' => $customer,
            'account' => $account,
            'statement' => $statement,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'lifetimeBilled' => $lifetimeBilled,
            'lifetimePaid' => $lifetimePaid,
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
    protected function exportStatementCsv(Customer $customer, array $statement)
    {
        $filename = 'customer_statement_' . $customer->id . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($customer, $statement) {
            $file = fopen('php://output', 'w');

            // Header info
            fputcsv($file, ['CUSTOMER LEDGER STATEMENT']);
            fputcsv($file, ['Customer Name:', $customer->name]);
            fputcsv($file, ['Mobile:', $customer->mobile]);
            fputcsv($file, ['Statement Generated At:', date('d-m-Y H:i')]);
            fputcsv($file, []);

            // Table headers
            fputcsv($file, ['Date', 'Reference No', 'Type', 'Description', 'Debit (Billed)', 'Credit (Paid)', 'Running Balance']);

            // Opening Balance row
            fputcsv($file, ['-', '-', 'Opening Balance', 'Opening Balance', '-', '-', number_format($statement['opening_balance'], 2, '.', '')]);

            // Data rows
            foreach ($statement['rows'] as $row) {
                fputcsv($file, [
                    $row['date'],
                    $row['reference_no'],
                    $row['reference_type'],
                    $row['description'],
                    $row['debit'] > 0 ? number_format($row['debit'], 2, '.', '') : '0.00',
                    $row['credit'] > 0 ? number_format($row['credit'], 2, '.', '') : '0.00',
                    number_format($row['running_balance'], 2, '.', ''),
                ]);
            }

            // Totals row
            fputcsv($file, [
                'TOTAL',
                '',
                '',
                '',
                number_format($statement['total_debit'], 2, '.', ''),
                number_format($statement['total_credit'], 2, '.', ''),
                number_format($statement['closing_balance'], 2, '.', ''),
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
