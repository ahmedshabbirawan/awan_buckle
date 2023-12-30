<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // code
            $table->string('code')->nullable();
            $table->string('ntn')->nullable();
            $table->string('email',251)->nullable();
            $table->string('phone',251)->nullable();
            $table->string('fax',251)->nullable();
            $table->string('address',251)->nullable();
            
            $table->integer('city_id')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('country_id')->nullable();

            $table->tinyInteger('status')->default('1');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->string('lastLoginIp',15)->nullable();
            $table->timestamp('lastLoginTime')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
