<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriesIdsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // threshold_quantity
            
            $table->integer('parent_cat_id')->nullable()->after('serial_no');
           // $table->integer('cat_id')->nullable(); //->change();
            $table->integer('sub_cat_id')->nullable();
            $table->integer('product_cat_id')->nullable();
            $table->integer('uom_id')->nullable();
            $table->integer('threshold_quantity')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
