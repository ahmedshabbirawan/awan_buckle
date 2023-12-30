<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateMeasureUnitColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('update_measure_unit_columns', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        Schema::table('measure_units', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){

        Schema::table('measure_units', function (Blueprint $table) {
            $table->dropColumn('key');
            $table->dropColumn('value');
        });
    }
}
