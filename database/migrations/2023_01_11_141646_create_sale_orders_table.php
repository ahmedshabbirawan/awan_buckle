<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sale_orders')) {
            Schema::create('sale_orders', function (Blueprint $table) {

            $table->id();
            $table->integer("customer_id")->nullable();
            $table->integer("sale_key")->nullable();
            $table->integer("shop_id")->nullable();            
            $table->integer("item_count")->nullable();
            $table->integer("total_offer_price")->nullable();
            $table->integer("total_discount")->nullable();
            $table->integer("total_price")->nullable();

            
            $table->integer("status")->default(1);
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
        Schema::dropIfExists('sales');
    }
}
