<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftWorkDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_work_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId("shift_id");
            $table->foreignId("shift_work_id");
            $table->integer("day");
            $table->dateTime("start_datetime");
            $table->dateTime("end_datetime");
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
        Schema::dropIfExists('shift_work_days');
    }
}
