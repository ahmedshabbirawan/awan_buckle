<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVehicleAndBiltyNumbersToSaleOrdersAndCartsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Sale Orders table
        if (Schema::hasTable('sale_orders')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_orders', 'vehicle_no')) {
                    $table->string('vehicle_no', 191)->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('sale_orders', 'bilty_no')) {
                    $table->string('bilty_no', 191)->nullable()->after('vehicle_no');
                }
            });
        }

        // 2. Sale Carts table
        if (Schema::hasTable('sale_carts')) {
            Schema::table('sale_carts', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_carts', 'vehicle_no')) {
                    $table->string('vehicle_no', 191)->nullable()->after('customer_id');
                }
                if (!Schema::hasColumn('sale_carts', 'bilty_no')) {
                    $table->string('bilty_no', 191)->nullable()->after('vehicle_no');
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
                if (Schema::hasColumn('sale_orders', 'bilty_no')) {
                    $table->dropColumn('bilty_no');
                }
                if (Schema::hasColumn('sale_orders', 'vehicle_no')) {
                    $table->dropColumn('vehicle_no');
                }
            });
        }

        if (Schema::hasTable('sale_carts')) {
            Schema::table('sale_carts', function (Blueprint $table) {
                if (Schema::hasColumn('sale_carts', 'bilty_no')) {
                    $table->dropColumn('bilty_no');
                }
                if (Schema::hasColumn('sale_carts', 'vehicle_no')) {
                    $table->dropColumn('vehicle_no');
                }
            });
        }
    }
}
