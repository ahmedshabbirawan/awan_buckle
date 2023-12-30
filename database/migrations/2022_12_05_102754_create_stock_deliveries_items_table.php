<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockDeliveriesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_deliveries_items', function (Blueprint $table) {
            $table->id();

            $table->string("ref_no",255)->nullable();

            $table->integer("stock_delivery_id")->nullable();
            $table->integer("product_cat_id")->nullable();
            $table->integer("product_id")->nullable();
            $table->integer("uom_id")->nullable();
            $table->integer("quantity")->nullable();
            $table->float("unit_price",8,2)->nullable();
            $table->date("warranty_date")->nullable();
            $table->date("expiry_date")->nullable();

            $table->integer("status")->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_deliveries_items');
    }
}
