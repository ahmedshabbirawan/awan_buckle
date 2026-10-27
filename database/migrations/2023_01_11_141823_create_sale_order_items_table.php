<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sale_order_items')) {
            Schema::create('sale_order_items', function (Blueprint $table) {
                $table->id();
                $table->integer("order_id")->nullable();
                $table->integer("product_id")->nullable();
                $table->integer("item_id")->nullable();
                $table->integer("sale_key")->nullable();

                $table->integer("sale_id")->nullable();
                $table->integer("customer_id")->nullable();

                $table->integer("qty")->nullable();

                $table->integer("offer_price")->nullable();
                $table->integer("discount")->nullable();
                $table->integer("price")->nullable();

                
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
        Schema::dropIfExists('sales_items');
    }
}
