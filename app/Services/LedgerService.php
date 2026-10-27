<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\LedgerTransaction;
use App\Models\Payment;
use App\Models\SaleOrder;
use App\Models\StockDelivery;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LedgerService
{
    /**
     * Retrieve or dynamically initialize an account
     */
    public function getOrCreateAccount(string $holderType, ?int $holderId = null, ?string $type = null, ?string $name = null, ?string $code = null): Account
    {
        if ($holderType === Account::HOLDER_INTERNAL) {
            $account = Account::where('code', $code)->first();
            if (!$account) {
                $account = Account::create([
                    'name' => $name ?? $code,
                    'code' => $code,
                    'type' => $type ?? Account::TYPE_ASSET,
                    'holder_type' => Account::HOLDER_INTERNAL,
                    'holder_id' => null,
                    'balance' => 0.00,
                    'status' => 1,
                ]);
            }
            return $account;
        }

        if ($holderType === Account::HOLDER_CUSTOMER) {
            $customer = Customer::findOrFail($holderId);
            $account = Account::where('holder_type', Account::HOLDER_CUSTOMER)
                ->where('holder_id', $holderId)
                ->first();

            if (!$account) {
                $account = Account::create([
                    'name' => 'Customer - ' . $customer->name,
                    'code' => 'AR_' . $customer->id,
                    'type' => Account::TYPE_ASSET, // Accounts Receivable is an Asset
                    'holder_type' => Account::HOLDER_CUSTOMER,
                    'holder_id' => $customer->id,
                    'balance' => 0.00,
                    'status' => 1,
                ]);
            }
            return $account;
        }

        if ($holderType === Account::HOLDER_SUPPLIER) {
            $supplier = Supplier::findOrFail($holderId);
            $account = Account::where('holder_type', Account::HOLDER_SUPPLIER)
                ->where('holder_id', $holderId)
                ->first();

            if (!$account) {
                $account = Account::create([
                    'name' => 'Supplier - ' . $supplier->name,
                    'code' => 'AP_' . $supplier->id,
                    'type' => Account::TYPE_LIABILITY, // Accounts Payable is a Liability
                    'holder_type' => Account::HOLDER_SUPPLIER,
                    'holder_id' => $supplier->id,
                    'balance' => 0.00,
                    'status' => 1,
                ]);
            }
            return $account;
        }

        throw new InvalidArgumentException("Unsupported account holder type: {$holderType}");
    }

    /**
     * Record a balanced double-entry transaction atomically.
     * Enforces: sum(debit) == sum(credit)
     *
     * @param array $txData [transaction_date, reference_type, reference_id, description, shop_id, created_by]
     * @param array $entries array of ['account_id' => int, 'debit' => float, 'credit' => float]
     * @return LedgerTransaction
     */
    public function recordTransaction(array $txData, array $entries): LedgerTransaction
    {
        return DB::transaction(function () use ($txData, $entries) {
            $totalDebit = 0.00;
            $totalCredit = 0.00;

            foreach ($entries as $e) {
                $debit = round((float) ($e['debit'] ?? 0.00), 2);
                $credit = round((float) ($e['credit'] ?? 0.00), 2);

                if ($debit < 0 || $credit < 0) {
                    throw new InvalidArgumentException("Debit and credit amounts cannot be negative.");
                }

                $totalDebit += $debit;
                $totalCredit += $credit;
            }

            $totalDebit = round($totalDebit, 2);
            $totalCredit = round($totalCredit, 2);

            if (abs($totalDebit - $totalCredit) > 0.001) {
                throw new InvalidArgumentException(
                    "Unbalanced ledger transaction! Total Debit ({$totalDebit}) != Total Credit ({$totalCredit})"
                );
            }

            if ($totalDebit <= 0) {
                throw new InvalidArgumentException("Transaction must have amounts greater than zero.");
            }

            $transaction = LedgerTransaction::create([
                'transaction_date' => $txData['transaction_date'] ?? now()->toDateString(),
                'reference_type' => $txData['reference_type'],
                'reference_id' => $txData['reference_id'] ?? null,
                'description' => $txData['description'] ?? null,
                'shop_id' => $txData['shop_id'] ?? null,
                'created_by' => $txData['created_by'] ?? (auth()->check() ? auth()->id() : null),
            ]);

            foreach ($entries as $e) {
                $debit = round((float) ($e['debit'] ?? 0.00), 2);
                $credit = round((float) ($e['credit'] ?? 0.00), 2);

                if ($debit == 0.00 && $credit == 0.00) {
                    continue;
                }

                LedgerEntry::create([
                    'ledger_transaction_id' => $transaction->id,
                    'account_id' => $e['account_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                ]);

                // Atomically update balance on the account
                $account = Account::find($e['account_id']);
                if ($account) {
                    if (in_array($account->type, [Account::TYPE_ASSET, Account::TYPE_EXPENSE])) {
                        $account->balance = round($account->balance + ($debit - $credit), 2);
                    } else {
                        $account->balance = round($account->balance + ($credit - $debit), 2);
                    }
                    $account->save();
                }
            }

            return $transaction;
        });
    }

    /**
     * Record Ledger for Vendor Purchase (Cash / Credit / Split)
     *
     * Rules:
     * - Debit: Inventory Asset (Total Purchase Value)
     * - Credit: Cash / Bank Account (Paid Amount) [if paid > 0]
     * - Credit: Supplier Accounts Payable (Due Amount) [if due > 0]
     */
    public function recordPurchaseLedger(StockDelivery $purchase, array $params = []): LedgerTransaction
    {
        $totalAmount = round((float) ($params['total_amount'] ?? $purchase->total_amount ?? $purchase->delivery_amount ?? 0), 2);
        $paidAmount = round((float) ($params['paid_amount'] ?? $purchase->paid_amount ?? 0), 2);
        $dueAmount = round((float) ($params['due_amount'] ?? $purchase->due_amount ?? 0), 2);
        $paymentMethod = $params['payment_method'] ?? $purchase->payment_method ?? 'cash';
        $paymentAccountCode = ($paymentMethod === 'bank_transfer') ? 'BANK' : 'CASH';

        if ($dueAmount > 0 && !$purchase->supplier_id) {
            throw new InvalidArgumentException("Vendor credit purchases require a valid supplier.");
        }

        if (abs(($paidAmount + $dueAmount) - $totalAmount) > 0.01) {
            throw new InvalidArgumentException(
                "Purchase payment mismatch: Paid ({$paidAmount}) + Due ({$dueAmount}) != Total ({$totalAmount})"
            );
        }

        $inventoryAccount = $this->getOrCreateAccount(Account::HOLDER_INTERNAL, null, Account::TYPE_ASSET, 'Inventory Asset', 'INVENTORY');
        $cashOrBankAccount = $this->getOrCreateAccount(Account::HOLDER_INTERNAL, null, Account::TYPE_ASSET, $paymentAccountCode === 'BANK' ? 'Bank Account' : 'Cash on Hand / Drawer', $paymentAccountCode);

        $entries = [];

        // 1. Debit Inventory Asset for total value
        $entries[] = [
            'account_id' => $inventoryAccount->id,
            'debit' => $totalAmount,
            'credit' => 0.00,
        ];

        // 2. Credit Cash / Bank for paid portion
        if ($paidAmount > 0) {
            $entries[] = [
                'account_id' => $cashOrBankAccount->id,
                'debit' => 0.00,
                'credit' => $paidAmount,
            ];
        }

        // 3. Credit Supplier AP for due portion
        if ($dueAmount > 0) {
            $supplierAccount = $this->getOrCreateAccount(Account::HOLDER_SUPPLIER, $purchase->supplier_id);
            $entries[] = [
                'account_id' => $supplierAccount->id,
                'debit' => 0.00,
                'credit' => $dueAmount,
            ];
        }

        $txData = [
            'transaction_date' => $purchase->purchased_date ? Carbon::parse($purchase->purchased_date)->toDateString() : now()->toDateString(),
            'reference_type' => LedgerTransaction::REF_PURCHASE,
            'reference_id' => $purchase->id,
            'description' => "Purchase #{$purchase->id} - Challan: {$purchase->delivery_challan_no} (Method: {$paymentMethod})",
            'shop_id' => $params['shop_id'] ?? null,
            'created_by' => auth()->check() ? auth()->id() : ($purchase->created_by ?? null),
        ];

        return $this->recordTransaction($txData, $entries);
    }

    /**
     * Record Ledger for Customer Sale (POS - Cash / Credit / Split)
     *
     * Rules:
     * - Debit: Cash / Bank Account (Paid Amount) [if paid > 0]
     * - Debit: Customer Accounts Receivable (Due Amount) [if due > 0]
     * - Credit: Sales Revenue Account (Total Sale Value)
     * - Optional Background: Debit COGS Expense, Credit Inventory Asset
     */
    public function recordSaleLedger(SaleOrder $sale, array $params = []): LedgerTransaction
    {
        $totalAmount = round((float) ($params['total_amount'] ?? $sale->total_amount ?? $sale->total_price ?? 0), 2);
        $paidAmount = round((float) ($params['paid_amount'] ?? $sale->paid_amount ?? 0), 2);
        $dueAmount = round((float) ($params['due_amount'] ?? $sale->due_amount ?? 0), 2);
        $paymentMethod = $params['payment_method'] ?? $sale->payment_method ?? 'cash';
        $paymentAccountCode = ($paymentMethod === 'bank_transfer') ? 'BANK' : 'CASH';

        // Non-Negotiable Rule: Credit sales cannot be assigned to generic/guest customers
        if ($dueAmount > 0 && (!$sale->customer_id || $sale->customer_id <= 0)) {
            throw new InvalidArgumentException(
                "Credit and split sales with a due balance require a registered customer. Guest checkout is prohibited for debt."
            );
        }

        if (abs(($paidAmount + $dueAmount) - $totalAmount) > 0.01) {
            throw new InvalidArgumentException(
                "Sale payment mismatch: Paid ({$paidAmount}) + Due ({$dueAmount}) != Total ({$totalAmount})"
            );
        }

        $revenueAccount = $this->getOrCreateAccount(Account::HOLDER_INTERNAL, null, Account::TYPE_REVENUE, 'Sales Revenue', 'SALES_REVENUE');
        $cashOrBankAccount = $this->getOrCreateAccount(Account::HOLDER_INTERNAL, null, Account::TYPE_ASSET, $paymentAccountCode === 'BANK' ? 'Bank Account' : 'Cash on Hand / Drawer', $paymentAccountCode);

        $entries = [];

        // 1. Debit Cash/Bank for paid amount
        if ($paidAmount > 0) {
            $entries[] = [
                'account_id' => $cashOrBankAccount->id,
                'debit' => $paidAmount,
                'credit' => 0.00,
            ];
        }

        // 2. Debit Customer AR for due amount
        if ($dueAmount > 0) {
            $customerAccount = $this->getOrCreateAccount(Account::HOLDER_CUSTOMER, $sale->customer_id);
            $entries[] = [
                'account_id' => $customerAccount->id,
                'debit' => $dueAmount,
                'credit' => 0.00,
            ];
        }

        // 3. Credit Sales Revenue for total amount
        $entries[] = [
            'account_id' => $revenueAccount->id,
            'debit' => 0.00,
            'credit' => $totalAmount,
        ];

        // 4. Background inventory credit / COGS debit if COGS calculated
        $cogsAmount = round((float) ($params['cogs_amount'] ?? 0), 2);
        if ($cogsAmount > 0) {
            $cogsAccount = $this->getOrCreateAccount(Account::HOLDER_INTERNAL, null, Account::TYPE_EXPENSE, 'Cost of Goods Sold', 'COGS');
            $inventoryAccount = $this->getOrCreateAccount(Account::HOLDER_INTERNAL, null, Account::TYPE_ASSET, 'Inventory Asset', 'INVENTORY');

            $entries[] = [
                'account_id' => $cogsAccount->id,
                'debit' => $cogsAmount,
                'credit' => 0.00,
            ];
            $entries[] = [
                'account_id' => $inventoryAccount->id,
                'debit' => 0.00,
                'credit' => $cogsAmount,
            ];
        }

        $txData = [
            'transaction_date' => now()->toDateString(),
            'reference_type' => LedgerTransaction::REF_SALE,
            'reference_id' => $sale->id,
            'description' => "POS Sale #{$sale->id} - Key: {$sale->sale_key} (Method: {$paymentMethod})",
            'shop_id' => $sale->shop_id ?? ($params['shop_id'] ?? null),
            'created_by' => auth()->check() ? auth()->id() : ($sale->created_by ?? null),
        ];

        return $this->recordTransaction($txData, $entries);
    }

    /**
     * Record a settlement payment or receipt voucher
     *
     * Customer Collection (Receipt):
     * - Debit: Cash / Bank Account (received_amount)
     * - Credit: Customer Accounts Receivable (received_amount)
     *
     * Vendor Settlement (Payment):
     * - Debit: Vendor Accounts Payable (paid_amount)
     * - Credit: Cash / Bank Account (paid_amount)
     */
    public function recordPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $partyType = $data['party_type']; // 'Customer' or 'Supplier'
            $partyId = (int) $data['party_id'];
            $amount = round((float) $data['amount'], 2);
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $paymentDate = $data['payment_date'] ?? now()->toDateString();
            $paymentAccountCode = ($paymentMethod === 'bank_transfer') ? 'BANK' : 'CASH';

            if ($amount <= 0) {
                throw new InvalidArgumentException("Payment amount must be greater than zero.");
            }

            $cashOrBankAccount = $this->getOrCreateAccount(
                Account::HOLDER_INTERNAL,
                null,
                Account::TYPE_ASSET,
                $paymentAccountCode === 'BANK' ? 'Bank Account' : 'Cash on Hand / Drawer',
                $paymentAccountCode
            );

            // Generate unique voucher number
            $prefix = ($partyType === Payment::PARTY_CUSTOMER) ? 'RCP' : 'PAY';
            $datePrefix = Carbon::parse($paymentDate)->format('Ymd');
            $countToday = Payment::whereDate('payment_date', $paymentDate)
                ->where('party_type', $partyType)
                ->count() + 1;
            $voucherNo = "{$prefix}-{$datePrefix}-" . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // Ensure voucher_no is strictly unique
            while (Payment::where('voucher_no', $voucherNo)->exists()) {
                $countToday++;
                $voucherNo = "{$prefix}-{$datePrefix}-" . str_pad($countToday, 4, '0', STR_PAD_LEFT);
            }

            $entries = [];

            if ($partyType === Payment::PARTY_CUSTOMER) {
                // Receipt Voucher: Debit Cash/Bank, Credit Customer AR
                $customerAccount = $this->getOrCreateAccount(Account::HOLDER_CUSTOMER, $partyId);

                $entries[] = [
                    'account_id' => $cashOrBankAccount->id,
                    'debit' => $amount,
                    'credit' => 0.00,
                ];
                $entries[] = [
                    'account_id' => $customerAccount->id,
                    'debit' => 0.00,
                    'credit' => $amount,
                ];

                $txData = [
                    'transaction_date' => $paymentDate,
                    'reference_type' => LedgerTransaction::REF_RECEIPT,
                    'description' => "Customer Receipt Voucher {$voucherNo}" . (!empty($data['notes']) ? " - " . $data['notes'] : ''),
                    'shop_id' => $data['shop_id'] ?? (auth()->check() ? auth()->user()->shop_id : null),
                    'created_by' => auth()->check() ? auth()->id() : null,
                ];
            } else {
                // Payment Voucher: Debit Supplier AP, Credit Cash/Bank
                $supplierAccount = $this->getOrCreateAccount(Account::HOLDER_SUPPLIER, $partyId);

                $entries[] = [
                    'account_id' => $supplierAccount->id,
                    'debit' => $amount,
                    'credit' => 0.00,
                ];
                $entries[] = [
                    'account_id' => $cashOrBankAccount->id,
                    'debit' => 0.00,
                    'credit' => $amount,
                ];

                $txData = [
                    'transaction_date' => $paymentDate,
                    'reference_type' => LedgerTransaction::REF_PAYMENT,
                    'description' => "Supplier Payout Voucher {$voucherNo}" . (!empty($data['notes']) ? " - " . $data['notes'] : ''),
                    'shop_id' => $data['shop_id'] ?? (auth()->check() ? auth()->user()->shop_id : null),
                    'created_by' => auth()->check() ? auth()->id() : null,
                ];
            }

            $tx = $this->recordTransaction($txData, $entries);

            $payment = Payment::create([
                'voucher_no' => $voucherNo,
                'party_type' => $partyType,
                'party_id' => $partyId,
                'payment_type' => ($partyType === Payment::PARTY_CUSTOMER) ? Payment::TYPE_RECEIPT : Payment::TYPE_PAYMENT,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payment_date' => $paymentDate,
                'reference_no' => $data['reference_no'] ?? null,
                'notes' => $data['notes'] ?? null,
                'ledger_transaction_id' => $tx->id,
                'shop_id' => $txData['shop_id'],
                'created_by' => $txData['created_by'],
            ]);

            // Link reference_id back to payment
            $tx->reference_id = $payment->id;
            $tx->save();

            return $payment;
        });
    }

    /**
     * Get Customer current outstanding receivable balance
     */
    public function getCustomerBalance(int $customerId): float
    {
        $account = Account::where('holder_type', Account::HOLDER_CUSTOMER)
            ->where('holder_id', $customerId)
            ->first();

        return $account ? $account->calculateBalance() : 0.00;
    }

    /**
     * Get Supplier current outstanding payable balance
     */
    public function getSupplierBalance(int $supplierId): float
    {
        $account = Account::where('holder_type', Account::HOLDER_SUPPLIER)
            ->where('holder_id', $supplierId)
            ->first();

        return $account ? $account->calculateBalance() : 0.00;
    }

    /**
     * Generate Account Statement with calculated running balance
     */
    public function getAccountStatement($account, ?string $startDate = null, ?string $endDate = null): array
    {
        if (is_numeric($account)) {
            $account = Account::findOrFail($account);
        }
        $isAssetOrExpense = in_array($account->type, [Account::TYPE_ASSET, Account::TYPE_EXPENSE]);

        // Opening Balance calculation for entries before startDate
        $openingBalance = 0.00;
        if ($startDate) {
            $priorTotals = LedgerEntry::where('account_id', $account->id)
                ->whereHas('transaction', function ($q) use ($startDate) {
                    $q->where('transaction_date', '<', $startDate);
                })
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
                ->first();

            $pDebit = (float) ($priorTotals->total_debit ?? 0);
            $pCredit = (float) ($priorTotals->total_credit ?? 0);

            $openingBalance = $isAssetOrExpense ? round($pDebit - $pCredit, 2) : round($pCredit - $pDebit, 2);
        }

        // Query entries within date range
        $entriesQuery = LedgerEntry::with('transaction')
            ->where('account_id', $account->id);

        if ($startDate) {
            $entriesQuery->whereHas('transaction', function ($q) use ($startDate) {
                $q->where('transaction_date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $entriesQuery->whereHas('transaction', function ($q) use ($endDate) {
                $q->where('transaction_date', '<=', $endDate);
            });
        }

        // Order ascending by transaction date and entry ID to construct running balance
        $entries = $entriesQuery->join('ledger_transactions', 'ledger_entries.ledger_transaction_id', '=', 'ledger_transactions.id')
            ->select('ledger_entries.*')
            ->orderBy('ledger_transactions.transaction_date', 'asc')
            ->orderBy('ledger_entries.id', 'asc')
            ->get();

        $running = $openingBalance;
        $statementRows = [];
        $totalDebit = 0.00;
        $totalCredit = 0.00;

        foreach ($entries as $entry) {
            $debit = (float) $entry->debit;
            $credit = (float) $entry->credit;

            $totalDebit += $debit;
            $totalCredit += $credit;

            if ($isAssetOrExpense) {
                $running = round($running + ($debit - $credit), 2);
            } else {
                $running = round($running + ($credit - $debit), 2);
            }

            $tx = $entry->transaction;

            $statementRows[] = [
                'id' => $entry->id,
                'date' => $tx ? Carbon::parse($tx->transaction_date)->format('d-m-Y') : '-',
                'raw_date' => $tx ? $tx->transaction_date : '',
                'reference_type' => $tx->reference_type ?? '-',
                'reference_id' => $tx->reference_id ?? null,
                'reference_no' => $this->formatReferenceNo($tx),
                'description' => $tx->description ?? '',
                'debit' => $debit,
                'credit' => $credit,
                'running_balance' => $running,
            ];
        }

        return [
            'account' => $account,
            'opening_balance' => $openingBalance,
            'closing_balance' => $running,
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'rows' => $statementRows,
        ];
    }

    /**
     * Generate Comprehensive Customer Statement
     * Includes all sales transactions (cash, credit, split, bank transfer) and payment receipt vouchers.
     */
    public function getCustomerStatement($customer, ?string $startDate = null, ?string $endDate = null): array
    {
        if (is_numeric($customer)) {
            $customer = Customer::findOrFail($customer);
        }

        $account = $this->getOrCreateAccount(Account::HOLDER_CUSTOMER, $customer->id);

        // 1. Calculate Opening Balance if startDate is provided
        $openingBalance = 0.00;
        if ($startDate) {
            $priorSales = SaleOrder::where('customer_id', $customer->id)
                ->whereDate('created_at', '<', $startDate)
                ->get();

            $priorDue = 0.00;
            foreach ($priorSales as $ps) {
                $pTotal = (float) ($ps->total_amount > 0 ? $ps->total_amount : ((float) $ps->total_price > 0 ? $ps->total_price : 0));
                $pPaid = (float) ($ps->paid_amount ?? 0);
                $pDue = (float) ($ps->due_amount ?? 0);
                $pMethod = strtolower($ps->payment_method ?? 'cash');

                if ($pTotal > 0 && $pPaid == 0 && $pDue == 0) {
                    if ($pMethod === 'credit') {
                        $pDue = $pTotal;
                    } elseif ($pMethod === 'cash' || $pMethod === 'bank_transfer') {
                        $pPaid = $pTotal;
                    } else {
                        $pPaid = $pTotal;
                    }
                }
                $priorDue += $pDue;
            }

            $priorReceipts = (float) Payment::where('party_type', Payment::PARTY_CUSTOMER)
                ->where('party_id', $customer->id)
                ->whereDate('payment_date', '<', $startDate)
                ->sum('amount');

            $priorAdjustments = (float) (LedgerEntry::where('account_id', $account->id)
                ->whereHas('transaction', function ($q) use ($startDate) {
                    $q->whereDate('transaction_date', '<', $startDate)
                      ->whereNotIn('reference_type', [LedgerTransaction::REF_SALE, LedgerTransaction::REF_RECEIPT, LedgerTransaction::REF_PAYMENT]);
                })
                ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as net_adj')
                ->value('net_adj') ?? 0);

            $openingBalance = round($priorDue - $priorReceipts + $priorAdjustments, 2);
        }

        // 2. Fetch Sales within date range
        $salesQuery = SaleOrder::where('customer_id', $customer->id);

        if ($startDate) {
            $salesQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $salesQuery->whereDate('created_at', '<=', $endDate);
        }

        $sales = $salesQuery->get();

        // 3. Fetch Payment Receipt Vouchers within date range
        $paymentsQuery = Payment::where('party_type', Payment::PARTY_CUSTOMER)
            ->where('party_id', $customer->id);

        if ($startDate) {
            $paymentsQuery->whereDate('payment_date', '>=', $startDate);
        }
        if ($endDate) {
            $paymentsQuery->whereDate('payment_date', '<=', $endDate);
        }

        $payments = $paymentsQuery->get();

        // 4. Fetch Adjustments within date range
        $adjustmentsQuery = LedgerEntry::where('account_id', $account->id)
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereNotIn('reference_type', [LedgerTransaction::REF_SALE, LedgerTransaction::REF_RECEIPT, LedgerTransaction::REF_PAYMENT]);
                if ($startDate) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            });

        $adjustments = $adjustmentsQuery->with('transaction')->get();

        // 5. Combine and normalize transactions
        $rawItems = [];

        foreach ($sales as $s) {
            $totalAmt = (float) ($s->total_amount > 0 ? $s->total_amount : ((float) $s->total_price > 0 ? $s->total_price : 0));
            $paidAmt = (float) ($s->paid_amount ?? 0);
            $dueAmt = (float) ($s->due_amount ?? 0);
            $method = strtolower($s->payment_method ?? 'cash');

            // Safety check if paid_amount / due_amount were unpopulated
            if ($totalAmt > 0 && $paidAmt == 0 && $dueAmt == 0) {
                if ($method === 'credit') {
                    $dueAmt = $totalAmt;
                } elseif ($method === 'cash' || $method === 'bank_transfer') {
                    $paidAmt = $totalAmt;
                } else {
                    $paidAmt = $totalAmt;
                }
            }

            if ($dueAmt <= 0) {
                if ($method === 'bank_transfer') {
                    $typeBadge = 'info';
                    $typeLabel = 'Online Sale (Paid)';
                } else {
                    $typeBadge = 'success';
                    $typeLabel = 'Cash Sale (Paid)';
                }
            } elseif ($paidAmt <= 0) {
                $typeBadge = 'danger';
                $typeLabel = 'Credit Sale';
            } else {
                $typeBadge = 'warning';
                $typeLabel = 'Split Sale';
            }

            $invNo = $s->sale_key ? (str_starts_with((string)$s->sale_key, 'INV') ? $s->sale_key : 'INV-' . $s->sale_key) : 'INV-' . str_pad($s->id, 5, '0', STR_PAD_LEFT);
            $sDate = $s->created_at ? $s->created_at->toDateString() : now()->toDateString();

            if ($method === 'bank_transfer') {
                $methodLabel = 'Bank Transfer / Online';
            } elseif ($method === 'credit') {
                $methodLabel = 'Credit';
            } elseif ($method === 'split') {
                $methodLabel = 'Split (Cash + Credit)';
            } else {
                $methodLabel = 'Cash';
            }

            $rawItems[] = [
                'sort_date' => $sDate,
                'sort_order' => 1,
                'id' => $s->id,
                'date' => $s->created_at ? $s->created_at->format('d-m-Y') : Carbon::parse($sDate)->format('d-m-Y'),
                'raw_date' => $sDate,
                'invoice_no' => $invNo,
                'reference_no' => $invNo,
                'reference_type' => 'Sale',
                'reference_id' => $s->id,
                'type_badge' => $typeBadge,
                'type_label' => $typeLabel,
                'payment_method' => $methodLabel,
                'raw_payment_method' => $method,
                'description' => "POS Sale #{$s->id}" . ($s->sale_key ? " - Ref: {$s->sale_key}" : "") . " (Method: {$methodLabel})",
                'total_amount' => $totalAmt,
                'paid_amount' => $paidAmt,
                'due_amount' => $dueAmt,
                'debit' => $dueAmt,
                'credit' => 0.00,
            ];
        }

        foreach ($payments as $pmt) {
            $amt = (float) $pmt->amount;
            $pmtDate = $pmt->payment_date ? Carbon::parse($pmt->payment_date)->toDateString() : ($pmt->created_at ? $pmt->created_at->toDateString() : now()->toDateString());
            $invNo = $pmt->voucher_no ?: 'RCP-' . str_pad($pmt->id, 5, '0', STR_PAD_LEFT);

            $method = strtolower($pmt->payment_method ?? 'cash');
            $methodLabel = ($method === 'bank_transfer') ? 'Bank Transfer / Online' : 'Cash';

            $rawItems[] = [
                'sort_date' => $pmtDate,
                'sort_order' => 2,
                'id' => $pmt->id,
                'date' => Carbon::parse($pmtDate)->format('d-m-Y'),
                'raw_date' => $pmtDate,
                'invoice_no' => $invNo,
                'reference_no' => $invNo,
                'reference_type' => 'Payment',
                'reference_id' => $pmt->id,
                'type_badge' => 'primary',
                'type_label' => 'Payment Receipt',
                'payment_method' => $methodLabel,
                'raw_payment_method' => $method,
                'description' => "Customer Receipt Voucher {$invNo}" . ($pmt->notes ? " - {$pmt->notes}" : ""),
                'total_amount' => 0.00,
                'paid_amount' => $amt,
                'due_amount' => 0.00,
                'debit' => 0.00,
                'credit' => $amt,
            ];
        }

        foreach ($adjustments as $adj) {
            $tx = $adj->transaction;
            $adjDate = $tx ? Carbon::parse($tx->transaction_date)->toDateString() : now()->toDateString();
            $deb = (float) $adj->debit;
            $crd = (float) $adj->credit;

            $rawItems[] = [
                'sort_date' => $adjDate,
                'sort_order' => 3,
                'id' => $adj->id,
                'date' => Carbon::parse($adjDate)->format('d-m-Y'),
                'raw_date' => $adjDate,
                'invoice_no' => $tx ? ($tx->reference_type . ' #' . ($tx->reference_id ?? $tx->id)) : 'ADJ-' . $adj->id,
                'reference_no' => $tx ? ($tx->reference_type . ' #' . ($tx->reference_id ?? $tx->id)) : 'ADJ-' . $adj->id,
                'reference_type' => 'Adjustment',
                'reference_id' => $tx->id ?? null,
                'type_badge' => 'inverse',
                'type_label' => 'Adjustment',
                'payment_method' => 'Adjustment',
                'raw_payment_method' => 'adjustment',
                'description' => $tx->description ?? 'Ledger Adjustment',
                'total_amount' => 0.00,
                'paid_amount' => $crd > 0 ? $crd : 0.00,
                'due_amount' => $deb > 0 ? $deb : 0.00,
                'debit' => $deb,
                'credit' => $crd,
            ];
        }

        // Sort chronologically
        usort($rawItems, function ($a, $b) {
            $cmp = strcmp($a['sort_date'], $b['sort_date']);
            if ($cmp !== 0) {
                return $cmp;
            }
            $orderCmp = $a['sort_order'] <=> $b['sort_order'];
            if ($orderCmp !== 0) {
                return $orderCmp;
            }
            return $a['id'] <=> $b['id'];
        });

        // 6. Build running balance and period totals
        $running = $openingBalance;
        $statementRows = [];

        $periodTotalSales = 0.00;
        $periodCashReceivedAtSale = 0.00;
        $periodOnlineReceivedAtSale = 0.00;
        $periodCreditSales = 0.00;
        $periodVoucherCash = 0.00;
        $periodVoucherOnline = 0.00;
        $periodTotalVouchers = 0.00;

        foreach ($rawItems as $row) {
            if ($row['reference_type'] === 'Sale') {
                $periodTotalSales += $row['total_amount'];
                $periodCreditSales += $row['due_amount'];

                if ($row['raw_payment_method'] === 'bank_transfer') {
                    $periodOnlineReceivedAtSale += $row['paid_amount'];
                } else {
                    $periodCashReceivedAtSale += $row['paid_amount'];
                }

                $running = round($running + $row['due_amount'], 2);
            } elseif ($row['reference_type'] === 'Payment') {
                $periodTotalVouchers += $row['paid_amount'];

                if ($row['raw_payment_method'] === 'bank_transfer') {
                    $periodVoucherOnline += $row['paid_amount'];
                } else {
                    $periodVoucherCash += $row['paid_amount'];
                }

                $running = round($running - $row['paid_amount'], 2);
            } else {
                $net = round($row['debit'] - $row['credit'], 2);
                $running = round($running + $net, 2);
            }

            $row['running_balance'] = $running;
            $statementRows[] = $row;
        }

        $periodCashReceived = round($periodCashReceivedAtSale + $periodVoucherCash, 2);
        $periodOnlineReceived = round($periodOnlineReceivedAtSale + $periodVoucherOnline, 2);
        $periodTotalCollected = round($periodCashReceived + $periodOnlineReceived, 2);

        // 7. Calculate Lifetime Totals
        $allLifetimeSales = SaleOrder::where('customer_id', $customer->id)->get();

        $lifetimeSales = 0.00;
        $lifetimeCashReceivedAtSale = 0.00;
        $lifetimeOnlineReceivedAtSale = 0.00;
        $lifetimeCreditSales = 0.00;

        foreach ($allLifetimeSales as $ls) {
            $t = (float) ($ls->total_amount > 0 ? $ls->total_amount : ((float) $ls->total_price > 0 ? $ls->total_price : 0));
            $p = (float) ($ls->paid_amount ?? 0);
            $d = (float) ($ls->due_amount ?? 0);
            $m = strtolower($ls->payment_method ?? 'cash');

            if ($t > 0 && $p == 0 && $d == 0) {
                if ($m === 'credit') {
                    $d = $t;
                } else {
                    $p = $t;
                }
            }

            $lifetimeSales += $t;
            $lifetimeCreditSales += $d;

            if ($m === 'bank_transfer') {
                $lifetimeOnlineReceivedAtSale += $p;
            } else {
                $lifetimeCashReceivedAtSale += $p;
            }
        }

        $allLifetimePayments = Payment::where('party_type', Payment::PARTY_CUSTOMER)
            ->where('party_id', $customer->id)
            ->get();

        $lifetimeVoucherCash = 0.00;
        $lifetimeVoucherOnline = 0.00;
        $lifetimeVoucherPaid = 0.00;

        foreach ($allLifetimePayments as $lp) {
            $amt = (float) $lp->amount;
            $lifetimeVoucherPaid += $amt;
            if (strtolower($lp->payment_method ?? 'cash') === 'bank_transfer') {
                $lifetimeVoucherOnline += $amt;
            } else {
                $lifetimeVoucherCash += $amt;
            }
        }

        $lifetimeCashReceived = round($lifetimeCashReceivedAtSale + $lifetimeVoucherCash, 2);
        $lifetimeOnlineReceived = round($lifetimeOnlineReceivedAtSale + $lifetimeVoucherOnline, 2);
        $lifetimeTotalCollected = round($lifetimeCashReceived + $lifetimeOnlineReceived, 2);

        $currentBalance = $this->getCustomerBalance($customer->id);

        return [
            'customer' => $customer,
            'account' => $account,
            'opening_balance' => $openingBalance,
            'closing_balance' => $running,

            // Period Totals
            'period_total_sales' => round($periodTotalSales, 2),
            'period_cash_received_at_sale' => round($periodCashReceivedAtSale, 2),
            'period_online_received_at_sale' => round($periodOnlineReceivedAtSale, 2),
            'period_credit_sales' => round($periodCreditSales, 2),
            'period_voucher_cash' => round($periodVoucherCash, 2),
            'period_voucher_online' => round($periodVoucherOnline, 2),
            'period_voucher_paid' => round($periodTotalVouchers, 2),
            'period_cash_received' => $periodCashReceived,
            'period_online_received' => $periodOnlineReceived,
            'period_total_collected' => $periodTotalCollected,
            'period_total_paid' => $periodTotalCollected,
            'total_debit' => round($periodCreditSales, 2),
            'total_credit' => round($periodTotalVouchers, 2),

            // Lifetime Totals (Required 6 metrics)
            'lifetime_sales' => round($lifetimeSales, 2),
            'lifetime_cash_received_at_sale' => round($lifetimeCashReceivedAtSale, 2),
            'lifetime_online_received_at_sale' => round($lifetimeOnlineReceivedAtSale, 2),
            'lifetime_cash_received' => $lifetimeCashReceived,
            'lifetime_online_received' => $lifetimeOnlineReceived,
            'lifetime_credit_sales' => round($lifetimeCreditSales, 2),
            'lifetime_voucher_cash' => round($lifetimeVoucherCash, 2),
            'lifetime_voucher_online' => round($lifetimeVoucherOnline, 2),
            'lifetime_voucher_paid' => round($lifetimeVoucherPaid, 2),
            'lifetime_total_collected' => $lifetimeTotalCollected,
            'current_balance' => $currentBalance,

            'rows' => $statementRows,
        ];
    }

    /**
     * Generate Comprehensive Supplier Statement
     * Includes all purchase transactions (cash, credit, split) and payment vouchers.
     */
    public function getSupplierStatement($supplier, ?string $startDate = null, ?string $endDate = null): array
    {
        if (is_numeric($supplier)) {
            $supplier = Supplier::findOrFail($supplier);
        }

        $account = $this->getOrCreateAccount(Account::HOLDER_SUPPLIER, $supplier->id);

        // 1. Calculate Opening Balance if startDate is provided
        $openingBalance = 0.00;
        if ($startDate) {
            $priorPurchases = StockDelivery::where('supplier_id', $supplier->id)
                ->where(function ($q) {
                    $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
                })
                ->whereDate('purchased_date', '<', $startDate)
                ->selectRaw('COALESCE(SUM(total_amount), 0) as total, COALESCE(SUM(paid_amount), 0) as paid, COALESCE(SUM(due_amount), 0) as due')
                ->first();

            $priorDue = (float) ($priorPurchases->due ?? 0);

            $priorPayments = (float) Payment::where('party_type', Payment::PARTY_SUPPLIER)
                ->where('party_id', $supplier->id)
                ->whereDate('payment_date', '<', $startDate)
                ->sum('amount');

            $priorAdjustments = (float) (LedgerEntry::where('account_id', $account->id)
                ->whereHas('transaction', function ($q) use ($startDate) {
                    $q->whereDate('transaction_date', '<', $startDate)
                      ->whereNotIn('reference_type', [LedgerTransaction::REF_PURCHASE, LedgerTransaction::REF_PAYMENT]);
                })
                ->selectRaw('COALESCE(SUM(credit), 0) - COALESCE(SUM(debit), 0) as net_adj')
                ->value('net_adj') ?? 0);

            $openingBalance = round($priorDue - $priorPayments + $priorAdjustments, 2);
        }

        // 2. Fetch Purchases within date range
        $purchasesQuery = StockDelivery::where('supplier_id', $supplier->id)
            ->where(function ($q) {
                $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
            });

        if ($startDate) {
            $purchasesQuery->whereDate('purchased_date', '>=', $startDate);
        }
        if ($endDate) {
            $purchasesQuery->whereDate('purchased_date', '<=', $endDate);
        }

        $purchases = $purchasesQuery->get();

        // 3. Fetch Payments within date range
        $paymentsQuery = Payment::where('party_type', Payment::PARTY_SUPPLIER)
            ->where('party_id', $supplier->id);

        if ($startDate) {
            $paymentsQuery->whereDate('payment_date', '>=', $startDate);
        }
        if ($endDate) {
            $paymentsQuery->whereDate('payment_date', '<=', $endDate);
        }

        $payments = $paymentsQuery->get();

        // 4. Fetch Adjustments within date range
        $adjustmentsQuery = LedgerEntry::where('account_id', $account->id)
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereNotIn('reference_type', [LedgerTransaction::REF_PURCHASE, LedgerTransaction::REF_PAYMENT]);
                if ($startDate) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            });

        $adjustments = $adjustmentsQuery->with('transaction')->get();

        // 5. Combine and normalize transactions
        $rawItems = [];

        foreach ($purchases as $p) {
            $totalAmt = (float) ($p->total_amount > 0 ? $p->total_amount : ((float) $p->delivery_amount > 0 ? $p->delivery_amount : 0));
            $paidAmt = (float) ($p->paid_amount ?? 0);
            $dueAmt = (float) ($p->due_amount ?? 0);
            $method = strtolower($p->payment_method ?? 'cash');

            // Safety check if paid_amount / due_amount were unpopulated
            if ($totalAmt > 0 && $paidAmt == 0 && $dueAmt == 0) {
                if ($method === 'credit') {
                    $dueAmt = $totalAmt;
                } elseif ($method === 'cash' || $method === 'bank_transfer') {
                    $paidAmt = $totalAmt;
                } else {
                    $dueAmt = $totalAmt;
                }
            }

            if ($dueAmt <= 0) {
                $typeBadge = 'success';
                $typeLabel = ($method === 'bank_transfer') ? 'Bank Purchase (Paid)' : 'Cash Purchase';
            } elseif ($paidAmt <= 0) {
                $typeBadge = 'danger';
                $typeLabel = 'Credit Purchase';
            } else {
                $typeBadge = 'warning';
                $typeLabel = 'Split Purchase';
            }

            $invNo = $p->delivery_challan_no ?: ($p->ref_no ?: 'PO-' . str_pad($p->id, 5, '0', STR_PAD_LEFT));
            $pDate = $p->purchased_date ? Carbon::parse($p->purchased_date)->toDateString() : ($p->created_at ? $p->created_at->toDateString() : now()->toDateString());

            $rawItems[] = [
                'sort_date' => $pDate,
                'sort_order' => 1,
                'id' => $p->id,
                'date' => Carbon::parse($pDate)->format('d-m-Y'),
                'raw_date' => $pDate,
                'invoice_no' => $invNo,
                'reference_no' => $invNo,
                'reference_type' => 'Purchase',
                'reference_id' => $p->id,
                'type_badge' => $typeBadge,
                'type_label' => $typeLabel,
                'payment_method' => ucfirst($p->payment_method ?? 'Cash'),
                'description' => "Purchase #{$p->id}" . ($p->delivery_challan_no ? " - Challan: {$p->delivery_challan_no}" : "") . " (Method: " . ucfirst($p->payment_method ?? 'cash') . ")",
                'total_amount' => $totalAmt,
                'paid_amount' => $paidAmt,
                'due_amount' => $dueAmt,
                'debit' => 0.00,
                'credit' => $dueAmt,
            ];
        }

        foreach ($payments as $pmt) {
            $amt = (float) $pmt->amount;
            $pmtDate = $pmt->payment_date ? Carbon::parse($pmt->payment_date)->toDateString() : ($pmt->created_at ? $pmt->created_at->toDateString() : now()->toDateString());
            $invNo = $pmt->voucher_no ?: 'PAY-' . str_pad($pmt->id, 5, '0', STR_PAD_LEFT);

            $rawItems[] = [
                'sort_date' => $pmtDate,
                'sort_order' => 2,
                'id' => $pmt->id,
                'date' => Carbon::parse($pmtDate)->format('d-m-Y'),
                'raw_date' => $pmtDate,
                'invoice_no' => $invNo,
                'reference_no' => $invNo,
                'reference_type' => 'Payment',
                'reference_id' => $pmt->id,
                'type_badge' => 'info',
                'type_label' => 'Payment Voucher',
                'payment_method' => ucfirst(str_replace('_', ' ', $pmt->payment_method ?? 'Cash')),
                'description' => "Supplier Payout Voucher {$invNo}" . ($pmt->notes ? " - {$pmt->notes}" : ""),
                'total_amount' => 0.00,
                'paid_amount' => $amt,
                'due_amount' => 0.00,
                'debit' => $amt,
                'credit' => 0.00,
            ];
        }

        foreach ($adjustments as $adj) {
            $tx = $adj->transaction;
            $adjDate = $tx ? Carbon::parse($tx->transaction_date)->toDateString() : now()->toDateString();
            $deb = (float) $adj->debit;
            $crd = (float) $adj->credit;
            $net = round($crd - $deb, 2);

            $rawItems[] = [
                'sort_date' => $adjDate,
                'sort_order' => 3,
                'id' => $adj->id,
                'date' => Carbon::parse($adjDate)->format('d-m-Y'),
                'raw_date' => $adjDate,
                'invoice_no' => $tx->reference_type . ' #' . ($tx->reference_id ?? $tx->id),
                'reference_no' => $tx->reference_type . ' #' . ($tx->reference_id ?? $tx->id),
                'reference_type' => 'Adjustment',
                'reference_id' => $tx->id ?? null,
                'type_badge' => 'inverse',
                'type_label' => 'Adjustment',
                'payment_method' => 'Adjustment',
                'description' => $tx->description ?? 'Ledger Adjustment',
                'total_amount' => 0.00,
                'paid_amount' => $deb > 0 ? $deb : 0.00,
                'due_amount' => $crd > 0 ? $crd : 0.00,
                'debit' => $deb,
                'credit' => $crd,
            ];
        }

        // Sort chronologically
        usort($rawItems, function ($a, $b) {
            $cmp = strcmp($a['sort_date'], $b['sort_date']);
            if ($cmp !== 0) {
                return $cmp;
            }
            $orderCmp = $a['sort_order'] <=> $b['sort_order'];
            if ($orderCmp !== 0) {
                return $orderCmp;
            }
            return $a['id'] <=> $b['id'];
        });

        // 6. Build running balance and period totals
        $running = $openingBalance;
        $statementRows = [];

        $periodPurchased = 0.00;
        $periodCashPaid = 0.00;
        $periodCreditPurchases = 0.00;
        $periodVoucherPaid = 0.00;

        foreach ($rawItems as $row) {
            if ($row['reference_type'] === 'Purchase') {
                $periodPurchased += $row['total_amount'];
                $periodCashPaid += $row['paid_amount'];
                $periodCreditPurchases += $row['due_amount'];
                $running = round($running + $row['due_amount'], 2);
            } elseif ($row['reference_type'] === 'Payment') {
                $periodVoucherPaid += $row['paid_amount'];
                $running = round($running - $row['paid_amount'], 2);
            } else {
                $net = round($row['credit'] - $row['debit'], 2);
                $running = round($running + $net, 2);
            }

            $row['running_balance'] = $running;
            $statementRows[] = $row;
        }

        $periodTotalPaid = round($periodCashPaid + $periodVoucherPaid, 2);

        // 7. Calculate Lifetime Totals
        $lifetimePurchased = (float) StockDelivery::where('supplier_id', $supplier->id)
            ->where(function ($q) {
                $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
            })
            ->sum('total_amount');

        $lifetimeCashPaid = (float) StockDelivery::where('supplier_id', $supplier->id)
            ->where(function ($q) {
                $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
            })
            ->sum('paid_amount');

        $lifetimeCreditPurchases = (float) StockDelivery::where('supplier_id', $supplier->id)
            ->where(function ($q) {
                $q->where('total_amount', '>', 0)->orWhere('delivery_amount', '>', 0);
            })
            ->sum('due_amount');

        $lifetimeVoucherPaid = (float) Payment::where('party_type', Payment::PARTY_SUPPLIER)
            ->where('party_id', $supplier->id)
            ->sum('amount');

        $lifetimeTotalPaid = round($lifetimeCashPaid + $lifetimeVoucherPaid, 2);
        $currentBalance = round($lifetimePurchased - $lifetimeTotalPaid, 2);

        return [
            'supplier' => $supplier,
            'account' => $account,
            'opening_balance' => $openingBalance,
            'closing_balance' => $running,
            'period_total_purchased' => round($periodPurchased, 2),
            'period_total_cash_paid' => round($periodCashPaid, 2),
            'period_total_credit_purchases' => round($periodCreditPurchases, 2),
            'period_total_voucher_paid' => round($periodVoucherPaid, 2),
            'period_total_paid' => $periodTotalPaid,
            'total_debit' => $periodTotalPaid,
            'total_credit' => round($periodPurchased, 2),
            'lifetime_purchased' => round($lifetimePurchased, 2),
            'lifetime_cash_paid' => round($lifetimeCashPaid, 2),
            'lifetime_credit_purchases' => round($lifetimeCreditPurchases, 2),
            'lifetime_voucher_paid' => round($lifetimeVoucherPaid, 2),
            'lifetime_total_paid' => $lifetimeTotalPaid,
            'current_balance' => $currentBalance,
            'rows' => $statementRows,
        ];
    }

    /**
     * Format reference number based on reference type
     */
    protected function formatReferenceNo(?LedgerTransaction $tx): string
    {
        if (!$tx) {
            return '-';
        }

        if ($tx->reference_type === LedgerTransaction::REF_SALE && $tx->reference_id) {
            return 'INV-' . str_pad($tx->reference_id, 5, '0', STR_PAD_LEFT);
        }

        if ($tx->reference_type === LedgerTransaction::REF_PURCHASE && $tx->reference_id) {
            return 'PO-' . str_pad($tx->reference_id, 5, '0', STR_PAD_LEFT);
        }

        if (in_array($tx->reference_type, [LedgerTransaction::REF_PAYMENT, LedgerTransaction::REF_RECEIPT]) && $tx->reference_id) {
            $payment = Payment::find($tx->reference_id);
            return $payment ? $payment->voucher_no : 'VCH-' . $tx->reference_id;
        }

        return $tx->reference_type . ' #' . ($tx->reference_id ?? $tx->id);
    }
}
