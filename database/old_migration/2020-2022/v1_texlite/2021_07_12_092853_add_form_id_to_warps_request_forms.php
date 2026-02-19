<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormIdToWarpsRequestForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warps_request_forms', function (Blueprint $table) {
            //
            $table->foreignId("form_id")->nullable()->comment("شماره فرم خروج از انبار صادر شده برای درخواست");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warps_request_forms', function (Blueprint $table) {
            //
        });
    }
}
