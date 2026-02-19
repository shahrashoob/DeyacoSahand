<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAllowTransactionToFinancialSystemFromTransKind extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trans_kinds', function (Blueprint $table) {
            //
            $table->dropColumn("allow_transaction_to_financial_system");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_system_from_trans_kind', function (Blueprint $table) {
            //
        });
    }
}
