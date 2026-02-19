<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasSeparatorInFullCodeToAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            //
            $table->integer("has_separator_in_full_code")->default(0)->comment("آیا در کد زیر حساب جدا کننده وجود دارد؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('full_code_to_accounts', function (Blueprint $table) {
            //
        });
    }
}
