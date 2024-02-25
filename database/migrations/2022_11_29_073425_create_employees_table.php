<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('org_id')->default('0');
            $table->string('emp_code')->default('20');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('designation')->nullable();
            
            $table->string('project')->nullable();
            $table->string('project_id')->nullable();
            $table->string('project_code')->nullable();

            $table->string('department')->nullable();
            $table->integer('designation_id')->nullable();
            // 

            $table->bigInteger("cnic")->nullable();
            $table->string('mobile')->nullable();
            
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->integer('city_id')->default('0');
            // $table->integer('ref_id')->default('0');
            $table->enum('source', ['pitb', 'non-pitb'])->default('pitb');
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('employees');
    }
}
