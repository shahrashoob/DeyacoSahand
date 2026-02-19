<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatetimeInShiftWorkDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_work_days', function (Blueprint $table) {
            //
            $table->datetime("start_datetime")->nullable()->change();
            $table->datetime("end_datetime")->nullable()->change();
        });
        Schema::table('new_shift_work_days', function (Blueprint $table) {
            //
            $table->datetime("start_datetime")->nullable()->change();
            $table->datetime("end_datetime")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_work_days', function (Blueprint $table) {
            //
        });
    }
}
