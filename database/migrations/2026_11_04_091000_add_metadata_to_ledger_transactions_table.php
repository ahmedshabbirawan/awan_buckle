<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetadataToLedgerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('ledger_transactions') && !Schema::hasColumn('ledger_transactions', 'metadata')) {
            Schema::table('ledger_transactions', function (Blueprint $table) {
                $table->json('metadata')->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('ledger_transactions') && Schema::hasColumn('ledger_transactions', 'metadata')) {
            Schema::table('ledger_transactions', function (Blueprint $table) {
                $table->dropColumn('metadata');
            });
        }
    }
}
