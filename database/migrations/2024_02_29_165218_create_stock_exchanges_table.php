<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('stock_exchanges')) {
            Schema::create('stock_exchanges', function (Blueprint $table) {
                // `id`, `exchange_key`, `shop_id`, `ref_no`, `stock_delivery_id`, `parent_cat_id`, `sub_cat_id`, `product_cat_id`, 
                // `product_id`, `uom_id`, `uom_code`, `req_qty`, `receive_qty`, `req_send_user_id`, `req_approve_user_id`, `req_send_shop_id`, 
                // `req_receive_shop_id`, `status`, `unit_price`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at`
                $table->id();
                $table->integer('exchange_key')->nullable();
                $table->integer('shop_id')->nullable();
                $table->string('ref_no')->nullable();
                $table->integer('product_id')->nullable();
                $table->integer('uom_code')->nullable();
                $table->integer('req_qty')->nullable();
                $table->integer('receive_qty')->nullable();
                $table->integer('req_send_user_id')->nullable();
                $table->integer('req_approve_user_id')->nullable();
                $table->integer('req_send_shop_id')->nullable();
                $table->integer('req_receive_shop_id')->nullable();
                $table->double('unit_price', 8, 2);
                $table->string('status')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->integer('deleted_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
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
        Schema::dropIfExists('stock_exchanges');
    }
}


