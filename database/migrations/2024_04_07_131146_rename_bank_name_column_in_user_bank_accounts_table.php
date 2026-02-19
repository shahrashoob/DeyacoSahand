<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBankNameColumnInUserBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_bank_accounts', function (Blueprint $table) {
            $table->renameColumn('bank_name', 'bank_name_remove');
            $table->foreignId('bank_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_bank_accounts', function (Blueprint $table) {
            $table->renameColumn('bank_name', 'bank_name_remove');
    });
    }
}
