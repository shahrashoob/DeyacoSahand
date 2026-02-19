<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewShiftWorkDayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_shift_work_day', function (Blueprint $table) {
            $table->id();
            $table->string("shift_caption");
            $table->string("shift_id");
            $table->string("shift_work_id");
            $table->string("day");
            $table->dateTime("start_datetime");
            $table->dateTime("end_datetime");
            $table->string("message");
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
        Schema::dropIfExists('new_shift_work_day');
    }
}
