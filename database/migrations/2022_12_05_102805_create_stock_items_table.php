<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->integer("stock_delivery_id")->nullable();
            $table->integer("stock_delivery_product_id")->nullable();
            $table->integer("sub_category_id")->nullable();
            $table->integer("product_cat_id")->nullable();
            $table->integer("product_id")->nullable();
            $table->integer("project_id")->nullable();
            $table->integer("shop_id")->nullable();
            $table->integer("qty")->nullable();
            
            $table->integer("available_qty")->nullable();
            $table->string("serial_number","191")->nullable();
        
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
        Schema::dropIfExists('stock_items');
    }
}
