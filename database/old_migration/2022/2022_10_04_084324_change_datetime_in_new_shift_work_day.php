<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatetimeInNewShiftWorkDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_shift_work_day', function (Blueprint $table) {
            //
            $table->dateTime("start_datetime")->nullable()->change();
            $table->dateTime("end_datetime")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_shift_work_day', function (Blueprint $table) {
            //
        });
    }
}
