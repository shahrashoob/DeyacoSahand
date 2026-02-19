<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('account_number')->comment("شماره حساب");
            $table->string('shaba_number')->comment("شماره شبا");
            $table->string('card_number')->nullable()->comment("شماره کارت");
            $table->string('bank_name')->comment("نام بانک");
            $table->string('bank_branch')->comment("نام شعبه");

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
        Schema::dropIfExists('user_bank_accounts');
    }
}
