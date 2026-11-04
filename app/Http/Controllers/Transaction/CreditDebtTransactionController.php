<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LedgerTransaction;
use App\Models\Supplier;
use App\Services\LedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreditDebtTransactionController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Store a new Customer Credit Transaction
     */
    public function storeCustomerCredit(Request $request, $customerId)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'reference_no' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|max:10240|mimes:jpeg,jpg,png,pdf,doc,docx,xls,xlsx,csv,txt',
        ]);

        $customer = Customer::findOrFail($customerId);

        $attachmentPath = null;
        $attachmentOriginalName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentOriginalName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $fileNameToStore = 'credit_' . $customer->id . '_' . time() . '_' . Str::random(6) . '.' . $ext;
            $attachmentPath = $file->storeAs('public/credit_debt_attachments', $fileNameToStore);
        }

        try {
            $tx = $this->ledgerService->recordCustomerCredit($customer, [
                'transaction_date' => Carbon::parse($request->input('transaction_date'))->toDateString(),
                'amount' => (float) $request->input('amount'),
                'description' => $request->input('description'),
                'reference_no' => $request->input('reference_no'),
                'attachment' => $attachmentPath,
                'attachment_name' => $attachmentOriginalName,
                'shop_id' => auth()->check() ? auth()->user()->shop_id : null,
            ]);

            $currentBalance = $this->ledgerService->getCustomerBalance($customer->id);

            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => "Customer credit transaction {$tx->transaction_id} recorded successfully! Outstanding balance updated to PKR " . number_format($currentBalance, 2),
                    'transaction' => $tx,
                    'current_balance' => $currentBalance,
                    'formatted_balance' => 'PKR ' . number_format($currentBalance, 2),
                ]);
            }

            return redirect()->back()->with('success', "Credit transaction {$tx->transaction_id} recorded successfully!");
        } catch (\Exception $e) {
            // Delete uploaded file if transaction fails
            if ($attachmentPath && Storage::exists($attachmentPath)) {
                Storage::delete($attachmentPath);
            }

            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a new Supplier Debt Transaction
     */
    public function storeSupplierDebt(Request $request, $supplierId)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'category' => 'required|in:borrowed_money,borrowed_goods,other_payable',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'reference_no' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|max:10240|mimes:jpeg,jpg,png,pdf,doc,docx,xls,xlsx,csv,txt',
        ]);

        $supplier = Supplier::findOrFail($supplierId);

        $attachmentPath = null;
        $attachmentOriginalName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentOriginalName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $fileNameToStore = 'debt_' . $supplier->id . '_' . time() . '_' . Str::random(6) . '.' . $ext;
            $attachmentPath = $file->storeAs('public/credit_debt_attachments', $fileNameToStore);
        }

        try {
            $tx = $this->ledgerService->recordSupplierDebt($supplier, [
                'transaction_date' => Carbon::parse($request->input('transaction_date'))->toDateString(),
                'category' => $request->input('category'),
                'amount' => (float) $request->input('amount'),
                'description' => $request->input('description'),
                'reference_no' => $request->input('reference_no'),
                'attachment' => $attachmentPath,
                'attachment_name' => $attachmentOriginalName,
                'shop_id' => auth()->check() ? auth()->user()->shop_id : null,
            ]);

            $currentBalance = $this->ledgerService->getSupplierBalance($supplier->id);

            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => "Supplier debt transaction {$tx->transaction_id} recorded successfully! Outstanding payable updated to PKR " . number_format($currentBalance, 2),
                    'transaction' => $tx,
                    'current_balance' => $currentBalance,
                    'formatted_balance' => 'PKR ' . number_format($currentBalance, 2),
                ]);
            }

            return redirect()->back()->with('success', "Debt transaction {$tx->transaction_id} recorded successfully!");
        } catch (\Exception $e) {
            if ($attachmentPath && Storage::exists($attachmentPath)) {
                Storage::delete($attachmentPath);
            }

            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * View Details of a Credit / Debt Transaction (JSON for Modal)
     */
    public function show($id)
    {
        $tx = LedgerTransaction::with(['creator', 'customer', 'supplier', 'entries.account'])->findOrFail($id);

        $partyName = ($tx->reference_type === LedgerTransaction::REF_CUSTOMER_CREDIT)
            ? optional($tx->customer)->name
            : optional($tx->supplier)->name;

        $partyType = ($tx->reference_type === LedgerTransaction::REF_CUSTOMER_CREDIT)
            ? 'customer'
            : 'supplier';

        $entries = [];
        if ($tx->entries) {
            foreach ($tx->entries as $entry) {
                $entries[] = [
                    'account_name' => optional($entry->account)->name ?? 'Account #' . $entry->account_id,
                    'account_code' => optional($entry->account)->code,
                    'debit' => (float) $entry->debit,
                    'credit' => (float) $entry->credit,
                    'formatted_debit' => 'PKR ' . number_format($entry->debit, 2),
                    'formatted_credit' => 'PKR ' . number_format($entry->credit, 2),
                ];
            }
        }

        return response()->json([
            'status' => true,
            'id' => $tx->id,
            'transaction_id' => $tx->transaction_id,
            'party_type' => $partyType,
            'party_name' => $partyName,
            'transaction_date' => $tx->transaction_date ? $tx->transaction_date->format('d-m-Y') : '-',
            'type' => $tx->type,
            'type_label' => $tx->type_label,
            'category' => $tx->category,
            'category_label' => $tx->category_label,
            'amount' => (float) $tx->amount,
            'formatted_amount' => 'PKR ' . number_format($tx->amount, 2),
            'balance_after' => (float) $tx->balance_after,
            'formatted_balance_after' => 'PKR ' . number_format($tx->balance_after, 2),
            'description' => $tx->remarks ?: 'No description provided',
            'reference_no' => $tx->reference_no ?: '-',
            'created_by_name' => optional($tx->creator)->name ?? 'System',
            'created_at' => $tx->created_at ? $tx->created_at->format('d-m-Y h:i A') : '-',
            'has_attachment' => !empty($tx->attachment),
            'attachment_name' => $tx->attachment_name ?: basename((string) $tx->attachment),
            'attachment_url' => !empty($tx->attachment) ? route('credit_debt_transaction.attachment', $tx->id) : null,
            'debit_amount' => $tx->debit_amount,
            'credit_amount' => $tx->credit_amount,
            'formatted_debit' => $tx->debit_amount > 0 ? 'PKR ' . number_format($tx->debit_amount, 2) : '-',
            'formatted_credit' => $tx->credit_amount > 0 ? 'PKR ' . number_format($tx->credit_amount, 2) : '-',
            'entries' => $entries,
        ]);
    }

    /**
     * Download or view attachment
     */
    public function downloadAttachment($id)
    {
        $tx = LedgerTransaction::findOrFail($id);

        if (!$tx->attachment || !Storage::exists($tx->attachment)) {
            abort(404, 'Attachment file not found.');
        }

        $displayName = $tx->attachment_name ?: basename($tx->attachment);
        return Storage::download($tx->attachment, $displayName);
    }
}
