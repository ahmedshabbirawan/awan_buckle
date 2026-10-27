<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFinancialFieldsToSalesAndPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. sale_orders table (Sales in this codebase)
        if (Schema::hasTable('sale_orders')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_orders', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0.00)->after('total_price');
                }
                if (!Schema::hasColumn('sale_orders', 'paid_amount')) {
                    $table->decimal('paid_amount', 12, 2)->default(0.00)->after('total_amount');
                }
                if (!Schema::hasColumn('sale_orders', 'due_amount')) {
                    $table->decimal('due_amount', 12, 2)->default(0.00)->after('paid_amount');
                }
                if (!Schema::hasColumn('sale_orders', 'payment_status')) {
                    $table->enum('payment_status', ['paid', 'partial', 'due'])->default('paid')->after('due_amount');
                }
                if (!Schema::hasColumn('sale_orders', 'payment_method')) {
                    $table->enum('payment_method', ['cash', 'credit', 'bank_transfer', 'split'])->default('cash')->after('payment_status');
                }
            });

            // Backfill existing sale_orders
            DB::statement("UPDATE sale_orders SET 
                total_amount = IFNULL(total_price, 0),
                paid_amount = IFNULL(total_price, 0),
                due_amount = 0.00,
                payment_status = 'paid',
                payment_method = 'cash'
                WHERE total_amount = 0.00 AND total_price > 0");
        }

        // 2. stock_deliveries table (Purchases in this codebase)
        if (Schema::hasTable('stock_deliveries')) {
            Schema::table('stock_deliveries', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_deliveries', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0.00)->after('delivery_amount');
                }
                if (!Schema::hasColumn('stock_deliveries', 'paid_amount')) {
                    $table->decimal('paid_amount', 12, 2)->default(0.00)->after('total_amount');
                }
                if (!Schema::hasColumn('stock_deliveries', 'due_amount')) {
                    $table->decimal('due_amount', 12, 2)->default(0.00)->after('paid_amount');
                }
                if (!Schema::hasColumn('stock_deliveries', 'payment_status')) {
                    $table->enum('payment_status', ['paid', 'partial', 'due'])->default('paid')->after('due_amount');
                }
                if (!Schema::hasColumn('stock_deliveries', 'payment_method')) {
                    $table->enum('payment_method', ['cash', 'credit', 'bank_transfer', 'split'])->default('cash')->after('payment_status');
                }
            });

            // Backfill existing stock_deliveries
            DB::statement("UPDATE stock_deliveries SET 
                total_amount = CAST(IFNULL(delivery_amount, 0) AS DECIMAL(12,2)),
                paid_amount = CAST(IFNULL(delivery_amount, 0) AS DECIMAL(12,2)),
                due_amount = 0.00,
                payment_status = 'paid',
                payment_method = 'cash'
                WHERE total_amount = 0.00 AND delivery_amount IS NOT NULL AND delivery_amount != ''");
        }

        // 3. Optional checks if tables named 'sales' or 'purchases' exist
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('sales', 'paid_amount')) {
                    $table->decimal('paid_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('sales', 'due_amount')) {
                    $table->decimal('due_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('sales', 'payment_status')) {
                    $table->enum('payment_status', ['paid', 'partial', 'due'])->default('paid');
                }
                if (!Schema::hasColumn('sales', 'payment_method')) {
                    $table->enum('payment_method', ['cash', 'credit', 'bank_transfer', 'split'])->default('cash');
                }
            });
        }

        if (Schema::hasTable('purchases')) {
            Schema::table('purchases', function (Blueprint $table) {
                if (!Schema::hasColumn('purchases', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'paid_amount')) {
                    $table->decimal('paid_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'due_amount')) {
                    $table->decimal('due_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'payment_status')) {
                    $table->enum('payment_status', ['paid', 'partial', 'due'])->default('paid');
                }
                if (!Schema::hasColumn('purchases', 'payment_method')) {
                    $table->enum('payment_method', ['cash', 'credit', 'bank_transfer', 'split'])->default('cash');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('sale_orders')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                $table->dropColumn(['total_amount', 'paid_amount', 'due_amount', 'payment_status', 'payment_method']);
            });
        }

        if (Schema::hasTable('stock_deliveries')) {
            Schema::table('stock_deliveries', function (Blueprint $table) {
                $table->dropColumn(['total_amount', 'paid_amount', 'due_amount', 'payment_status', 'payment_method']);
            });
        }
    }
}
