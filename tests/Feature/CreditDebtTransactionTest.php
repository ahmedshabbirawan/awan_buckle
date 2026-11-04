<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\LedgerTransaction;
use App\Models\Payment;
use App\Models\Supplier;
use App\Models\User;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CreditDebtTransactionTest extends TestCase
{
    use DatabaseTransactions;

    protected LedgerService $ledgerService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ledgerService = app(LedgerService::class);

        $this->user = User::first() ?? User::factory()->create();
    }

    /**
     * Verify that no credit_debt_transactions table exists in the database (Approach 1)
     */
    public function test_no_credit_debt_transactions_table_exists()
    {
        $this->assertFalse(Schema::hasTable('credit_debt_transactions'), 'credit_debt_transactions table should NOT exist in Approach 1.');
    }

    /**
     * Test Example 1 from User Request: Customer Credit Transaction
     * Customer has 0 balance. Record Rs. 500 receivable.
     * Outstanding balance becomes 500.
     * Cash/Bank untouched.
     * Future settlement reduces balance.
     */
    public function test_customer_credit_transaction_and_settlement()
    {
        $this->actingAs($this->user);

        // 1. Create a fresh test customer
        $customer = Customer::create([
            'name' => 'Test Customer ' . uniqid(),
            'mobile' => '03001234567',
            'status' => 1,
        ]);

        $initialBalance = $this->ledgerService->getCustomerBalance($customer->id);
        $this->assertEquals(0.00, $initialBalance);

        // Record initial cash and bank balances
        $cashAcc = Account::where('code', 'CASH')->first();
        $bankAcc = Account::where('code', 'BANK')->first();
        $initialCash = $cashAcc ? (float) $cashAcc->balance : 0.00;
        $initialBank = $bankAcc ? (float) $bankAcc->balance : 0.00;

        // 2. Post Customer Credit Transaction of Rs. 500
        $response = $this->postJson(route('customer.credit_transaction.store', $customer->id), [
            'transaction_date' => now()->toDateString(),
            'amount' => 500.00,
            'description' => 'Test customer owes Rs. 500',
            'reference_no' => 'REF-500',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'current_balance' => 500.00,
        ]);

        // 3. Verify Customer's outstanding balance increased by 500
        $newBalance = $this->ledgerService->getCustomerBalance($customer->id);
        $this->assertEquals(500.00, $newBalance);

        // 4. Verify LedgerTransaction record directly (No new table)
        $creditTx = LedgerTransaction::with(['entries.account', 'creator'])
            ->where('reference_type', LedgerTransaction::REF_CUSTOMER_CREDIT)
            ->where('reference_id', $customer->id)
            ->first();

        $this->assertNotNull($creditTx);
        $this->assertStringStartsWith('CR-', $creditTx->transaction_id);
        $this->assertEquals(500.00, (float) $creditTx->amount);
        $this->assertEquals(500.00, (float) $creditTx->balance_after);
        $this->assertEquals('credit', $creditTx->type);
        $this->assertEquals('Credit / Amount Receivable', $creditTx->type_label);
        $this->assertStringContainsString('Customer Credit: CR-', $creditTx->description);
        $this->assertStringContainsString('Test customer owes Rs. 500', $creditTx->description);
        $this->assertEquals('Test customer owes Rs. 500', $creditTx->remarks);
        $this->assertEquals('REF-500', $creditTx->reference_no);
        $this->assertIsArray($creditTx->metadata);
        $this->assertEquals(500.00, $creditTx->metadata['amount']);

        // 5. Verify Double Entry Ledger Entries
        $customerAccount = Account::where('holder_type', Account::HOLDER_CUSTOMER)->where('holder_id', $customer->id)->first();
        $clearingAccount = Account::where('code', 'MANUAL_ADJUSTMENT')->first();

        $customerEntry = $creditTx->entries->where('account_id', $customerAccount->id)->first();
        $clearingEntry = $creditTx->entries->where('account_id', $clearingAccount->id)->first();

        $this->assertNotNull($customerEntry);
        $this->assertEquals(500.00, (float) $customerEntry->debit);
        $this->assertEquals(0.00, (float) $customerEntry->credit);

        $this->assertNotNull($clearingEntry);
        $this->assertEquals(0.00, (float) $clearingEntry->debit);
        $this->assertEquals(500.00, (float) $clearingEntry->credit);

        // 6. Verify Cash & Bank accounts are untouched
        $cashAcc->refresh();
        $bankAcc->refresh();
        $this->assertEquals($initialCash, (float) $cashAcc->balance);
        $this->assertEquals($initialBank, (float) $bankAcc->balance);

        // 7. Verify Customer Statement contains this transaction
        $statement = $this->ledgerService->getCustomerStatement($customer);
        $this->assertEquals(500.00, $statement['current_balance']);
        $this->assertEquals(500.00, $statement['closing_balance']);

        $stmtRow = collect($statement['rows'])->where('reference_id', $creditTx->id)->first();
        $this->assertNotNull($stmtRow);
        $this->assertEquals(500.00, $stmtRow['due_amount']);
        $this->assertEquals(500.00, $stmtRow['running_balance']);

        // 8. Test Details Endpoint
        $detailResponse = $this->getJson(route('credit_debt_transaction.show', $creditTx->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertJson([
            'status' => true,
            'transaction_id' => $creditTx->transaction_id,
            'amount' => 500.00,
            'balance_after' => 500.00,
            'debit_amount' => 500.00,
            'credit_amount' => 0.00,
        ]);

        // 9. Test Future Settlement: Customer pays Rs. 500 via regular workflow
        $payment = $this->ledgerService->recordPayment([
            'party_type' => Payment::PARTY_CUSTOMER,
            'party_id' => $customer->id,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'notes' => 'Settling manual credit',
        ]);

        $this->assertNotNull($payment);

        // Customer balance must now be settled back to 0.00
        $settledBalance = $this->ledgerService->getCustomerBalance($customer->id);
        $this->assertEquals(0.00, $settledBalance);
    }

    /**
     * Test Example 2 & 3 from User Request: Supplier Debt Transactions
     * - Example 1: Borrowed Money Rs. 5,000
     * - Example 2: Borrowed Goods Rs. 10,000
     * Outstanding balance becomes 15,000.
     * Cash/Bank untouched by debt creation.
     * Payout voucher settles balance.
     */
    public function test_supplier_debt_transactions_and_settlement()
    {
        $this->actingAs($this->user);

        // 1. Create a fresh test supplier
        $supplier = Supplier::create([
            'name' => 'Test Supplier ' . uniqid(),
            'phone' => '03211234567',
            'status' => 1,
        ]);

        $initialBalance = $this->ledgerService->getSupplierBalance($supplier->id);
        $this->assertEquals(0.00, $initialBalance);

        $cashAcc = Account::where('code', 'CASH')->first();
        $initialCash = $cashAcc ? (float) $cashAcc->balance : 0.00;

        // 2. Example 1: Borrowed Money Rs. 5,000
        $resp1 = $this->postJson(route('supplier.debt_transaction.store', $supplier->id), [
            'transaction_date' => now()->toDateString(),
            'category' => LedgerTransaction::CATEGORY_BORROWED_MONEY,
            'amount' => 5000.00,
            'description' => 'Borrowed Rs. 5000 cash from supplier',
            'reference_no' => 'BORROW-1',
        ]);

        $resp1->assertStatus(200);
        $resp1->assertJson([
            'status' => true,
            'current_balance' => 5000.00,
        ]);

        $balanceAfter1 = $this->ledgerService->getSupplierBalance($supplier->id);
        $this->assertEquals(5000.00, $balanceAfter1);

        $debtTx1 = LedgerTransaction::with(['entries.account', 'creator'])
            ->where('reference_type', LedgerTransaction::REF_SUPPLIER_DEBT)
            ->where('reference_id', $supplier->id)
            ->first();

        $this->assertNotNull($debtTx1);
        $this->assertStringStartsWith('DB-', $debtTx1->transaction_id);
        $this->assertEquals('Borrowed Money', $debtTx1->category_label);
        $this->assertEquals('Debt / Amount Payable', $debtTx1->type_label);
        $this->assertEquals(5000.00, (float) $debtTx1->amount);
        $this->assertEquals(5000.00, (float) $debtTx1->balance_after);
        $this->assertStringContainsString('Supplier Debt (Borrowed Money): DB-', $debtTx1->description);
        $this->assertStringContainsString('Borrowed Rs. 5000 cash from supplier', $debtTx1->description);
        $this->assertEquals('Borrowed Rs. 5000 cash from supplier', $debtTx1->remarks);
        $this->assertIsArray($debtTx1->metadata);
        $this->assertEquals(5000.00, $debtTx1->metadata['amount']);

        // 3. Example 2: Borrowed Goods Rs. 10,000
        $resp2 = $this->postJson(route('supplier.debt_transaction.store', $supplier->id), [
            'transaction_date' => now()->toDateString(),
            'category' => LedgerTransaction::CATEGORY_BORROWED_GOODS,
            'amount' => 10000.00,
            'description' => 'Received goods worth 10,000 without payment',
            'reference_no' => 'GOODS-10K',
        ]);

        $resp2->assertStatus(200);
        $resp2->assertJson([
            'status' => true,
            'current_balance' => 15000.00,
        ]);

        // Total payable is now 15,000
        $balanceAfter2 = $this->ledgerService->getSupplierBalance($supplier->id);
        $this->assertEquals(15000.00, $balanceAfter2);

        // Verify Cash Account remained untouched during debt recordings
        $cashAcc->refresh();
        $this->assertEquals($initialCash, (float) $cashAcc->balance);

        // 4. Verify Supplier Statement contains both transactions
        $statement = $this->ledgerService->getSupplierStatement($supplier);
        $this->assertEquals(15000.00, $statement['current_balance']);
        $this->assertEquals(15000.00, $statement['closing_balance']);

        $borrowRow = collect($statement['rows'])->where('reference_id', $debtTx1->id)->first();
        $this->assertNotNull($borrowRow);
        $this->assertEquals(5000.00, $borrowRow['due_amount']);
        $this->assertEquals(5000.00, $borrowRow['running_balance']);

        // 5. Test Settlement: Pay Supplier Rs. 15,000 via settlement workflow
        $payout = $this->ledgerService->recordPayment([
            'party_type' => Payment::PARTY_SUPPLIER,
            'party_id' => $supplier->id,
            'amount' => 15000.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'notes' => 'Full settlement of borrowed money and goods',
        ]);

        $this->assertNotNull($payout);

        // Supplier balance must now be fully settled to 0.00
        $settledBalance = $this->ledgerService->getSupplierBalance($supplier->id);
        $this->assertEquals(0.00, $settledBalance);
    }
}
