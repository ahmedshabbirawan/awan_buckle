<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            /*
                product.cat_id
                product_code
                product_name
                description
                uom_id
                threshold_qty
            */
            $table->string('name');
            $table->string('code')->nullable();
            $table->double('price', 8, 2);
            $table->text('description')->nullable();
            $table->integer('product_cat_id')->nullable();
            $table->integer('uom_id')->nullable();
            $table->integer("threshold_qty")->nullable();
            $table->string('image')->nullable();
            $table->integer("status")->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
