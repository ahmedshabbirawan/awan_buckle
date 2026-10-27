<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDoubleEntryLedgerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Accounts Table
        if (!Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('code', 50)->nullable()->unique();
                $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
                $table->string('holder_type', 50)->nullable(); // Customer, Supplier, Internal
                $table->unsignedBigInteger('holder_id')->nullable();
                $table->decimal('balance', 12, 2)->default(0.00);
                $table->tinyInteger('status')->default(1);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->index(['holder_type', 'holder_id']);
                $table->index('type');
            });
        }

        // 2. Ledger Transactions Table
        if (!Schema::hasTable('ledger_transactions')) {
            Schema::create('ledger_transactions', function (Blueprint $table) {
                $table->id();
                $table->date('transaction_date');
                $table->string('reference_type', 50); // Sale, Purchase, Payment, Receipt, Adjustment
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('description', 255)->nullable();
                $table->unsignedBigInteger('shop_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['reference_type', 'reference_id']);
                $table->index('transaction_date');
                $table->index('shop_id');
            });
        }

        // 3. Ledger Entries Table
        if (!Schema::hasTable('ledger_entries')) {
            Schema::create('ledger_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ledger_transaction_id');
                $table->unsignedBigInteger('account_id');
                $table->decimal('debit', 12, 2)->default(0.00);
                $table->decimal('credit', 12, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('ledger_transaction_id')
                    ->references('id')
                    ->on('ledger_transactions')
                    ->onDelete('cascade');

                $table->foreign('account_id')
                    ->references('id')
                    ->on('accounts')
                    ->onDelete('restrict');

                $table->index(['account_id', 'created_at']);
            });
        }

        // 4. Payments Table (Settlement Vouchers)
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->string('voucher_no', 50)->unique();
                $table->enum('party_type', ['Customer', 'Supplier']);
                $table->unsignedBigInteger('party_id');
                $table->enum('payment_type', ['receipt', 'payment']); // receipt for customer collection, payment for supplier payout
                $table->decimal('amount', 12, 2);
                $table->string('payment_method', 50)->default('cash'); // cash, bank_transfer, cheque
                $table->date('payment_date');
                $table->string('reference_no', 100)->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('ledger_transaction_id')->nullable();
                $table->unsignedBigInteger('shop_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['party_type', 'party_id']);
                $table->index('payment_date');
            });
        }

        // Seed default internal accounts
        $defaultAccounts = [
            [
                'name' => 'Cash on Hand / Drawer',
                'code' => 'CASH',
                'type' => 'asset',
                'holder_type' => 'Internal',
                'holder_id' => null,
                'balance' => 0.00,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank Account',
                'code' => 'BANK',
                'type' => 'asset',
                'holder_type' => 'Internal',
                'holder_id' => null,
                'balance' => 0.00,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Inventory Asset',
                'code' => 'INVENTORY',
                'type' => 'asset',
                'holder_type' => 'Internal',
                'holder_id' => null,
                'balance' => 0.00,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales Revenue',
                'code' => 'SALES_REVENUE',
                'type' => 'revenue',
                'holder_type' => 'Internal',
                'holder_id' => null,
                'balance' => 0.00,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cost of Goods Sold',
                'code' => 'COGS',
                'type' => 'expense',
                'holder_type' => 'Internal',
                'holder_id' => null,
                'balance' => 0.00,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($defaultAccounts as $acc) {
            DB::table('accounts')->updateOrInsert(['code' => $acc['code']], $acc);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('ledger_transactions');
        Schema::dropIfExists('accounts');
    }
}
