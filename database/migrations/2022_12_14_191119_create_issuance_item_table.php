<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuanceItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issuance_item', function (Blueprint $table) {
            $table->id();
            $table->integer("item_id")->nullable();
            $table->integer("project_id")->nullable();
            $table->integer("emp_id")->nullable();
            
            $table->integer("product_id")->nullable();
            $table->integer("product_cat_id")->nullable();
            $table->integer("sub_cat_id")->nullable();
            $table->string("serial_number")->nullable();
            // serial_number
            $table->integer("qty")->nullable();

            // 
            $table->string("issue_key")->nullable();
            $table->string("hard_copy_file")->nullable();
            
            $table->tinyInteger('is_loan')->default('0');
            $table->tinyInteger('is_data_center')->default('0');
            $table->text('remarks')->nullable();

            $table->date("issue_date")->nullable();
            $table->date("loan_return_date")->nullable();
            $table->date("return_date")->nullable();

            
           //  $table->integer("return_condition_status")->nullable();


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
        Schema::dropIfExists('issuance_item');
    }
}
