<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRowIdAndDateToShiftWorkDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_shift_work_days', function (Blueprint $table) {
            //
            $table->integer("row_id");
            $table->string("datetime");
        });
        Schema::table('shift_work_days', function (Blueprint $table) {
            //
            $table->date("datetime");
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
