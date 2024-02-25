<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('return_details', function (Blueprint $table) {
            $table->id();
            $table->integer("emp_id")->nullable();
            $table->integer("item_return_count")->nullable();
            $table->string("item_return_ids")->nullable();
            $table->string("return_report_file")->nullable();
            $table->string("remarks")->nullable();
            $table->integer("status")->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_details');
    }
}
