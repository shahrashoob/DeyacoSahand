<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNosaCodeAndAllowDeleteToLotNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lot_numbers', function (Blueprint $table) {
            //
            $table->string("nosa_code")->nullable()->comment("کد لات در نوسا");
            $table->boolean("allow_delete")->default(false)->comment("کد های سیستمی قابل حذف نیستند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lot_numbers', function (Blueprint $table) {
            //
        });
    }
}
