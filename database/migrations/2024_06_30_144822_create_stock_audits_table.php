<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_audits', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->string('action')->nullable();
            $table->integer('shop_id')->nullable();
            $table->integer('old_qty')->nullable();
            $table->integer('update_qty')->nullable();
            $table->integer('current_qty')->nullable();
            $table->string('object_type')->nullable();
            $table->integer('object_id')->nullable();
            $table->tinyText('stock_old_value')->nullable();
            $table->tinyText('stock_new_value')->nullable();
            $table->tinyText('description')->nullable();
            $table->tinyText('extra_value')->nullable();
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
        Schema::dropIfExists('stock_audits');
    }
}
