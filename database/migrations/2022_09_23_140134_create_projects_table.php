<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('dg')->nullable();
            $table->string('code',251)->nullable();
           // 
            $table->integer('manager_id')->nullable(); 
            $table->integer('org_id')->nullable(); // ->default('0');
            $table->string('detail')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->string('lastLoginIp',15)->nullable();
            $table->timestamp('lastLoginTime')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
