<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('loc_id')->default('0');
            $table->enum('type', ['CO', 'PR', 'CI']);
            // $table->float('lat', 8, 2);
            // $table->string('image', 191)->nullable();
            // $table->geometryCollection('positions');
            // $table->geometry('positions1');
            // $table->softDeletes()->index();
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
        Schema::dropIfExists('locations');
    }
}
