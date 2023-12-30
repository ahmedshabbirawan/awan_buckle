<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnDateInIssuanceItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issuance_item', function (Blueprint $table) {
            $table->integer("return_detail_id")->nullable();
            $table->integer("return_condition_status")->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issuance_item', function (Blueprint $table) {
            //
        });
    }
}
