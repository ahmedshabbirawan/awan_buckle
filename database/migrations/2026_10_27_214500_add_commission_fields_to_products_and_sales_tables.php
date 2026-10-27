<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionFieldsToProductsAndSalesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Products table
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'commission_percentage')) {
                    $table->decimal('commission_percentage', 8, 2)->default(0.00)->after('price');
                }
            });
        }

        // 2. Sale Carts table
        if (Schema::hasTable('sale_carts')) {
            Schema::table('sale_carts', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_carts', 'commission_percentage')) {
                    $table->decimal('commission_percentage', 8, 2)->default(0.00)->after('discount');
                }
                if (!Schema::hasColumn('sale_carts', 'commission_amount')) {
                    $table->decimal('commission_amount', 12, 2)->default(0.00)->after('commission_percentage');
                }
                if (!Schema::hasColumn('sale_carts', 'is_commission_applied')) {
                    $table->boolean('is_commission_applied')->default(false)->after('commission_amount');
                }
            });
        }

        // 3. Sale Orders table
        if (Schema::hasTable('sale_orders')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_orders', 'total_commission')) {
                    $table->decimal('total_commission', 12, 2)->default(0.00)->after('total_discount');
                }
            });
        }

        // 4. Sale Order Items table
        if (Schema::hasTable('sale_order_items')) {
            Schema::table('sale_order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_order_items', 'commission_percentage')) {
                    $table->decimal('commission_percentage', 8, 2)->default(0.00)->after('discount');
                }
                if (!Schema::hasColumn('sale_order_items', 'commission_amount')) {
                    $table->decimal('commission_amount', 12, 2)->default(0.00)->after('commission_percentage');
                }
                if (!Schema::hasColumn('sale_order_items', 'is_commission_applied')) {
                    $table->boolean('is_commission_applied')->default(false)->after('commission_amount');
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
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'commission_percentage')) {
                    $table->dropColumn('commission_percentage');
                }
            });
        }

        if (Schema::hasTable('sale_carts')) {
            Schema::table('sale_carts', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('sale_carts', 'commission_percentage')) $cols[] = 'commission_percentage';
                if (Schema::hasColumn('sale_carts', 'commission_amount')) $cols[] = 'commission_amount';
                if (Schema::hasColumn('sale_carts', 'is_commission_applied')) $cols[] = 'is_commission_applied';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('sale_orders')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                if (Schema::hasColumn('sale_orders', 'total_commission')) {
                    $table->dropColumn('total_commission');
                }
            });
        }

        if (Schema::hasTable('sale_order_items')) {
            Schema::table('sale_order_items', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('sale_order_items', 'commission_percentage')) $cols[] = 'commission_percentage';
                if (Schema::hasColumn('sale_order_items', 'commission_amount')) $cols[] = 'commission_amount';
                if (Schema::hasColumn('sale_order_items', 'is_commission_applied')) $cols[] = 'is_commission_applied';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
}
