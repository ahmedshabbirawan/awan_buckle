<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $fillable = ['product_id', 'shop_id', 'user_id', 'qty_when_adjust', 'qty', 'stock_type','created_by','description'
        ,'updated_by','deleted_by','deleted_at','created_at','updated_at']; 

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();

            $table->integer('product_id')->nullable();
            $table->integer('shop_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('qty_when_adjust')->nullable();
            $table->integer('qty')->nullable();
            $table->string('stock_type')->nullable();
            $table->double('purchase_price', 8, 2);
            $table->double('sale_price', 8, 2);
            $table->text('description')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('stock_adjustments');
    }
}
