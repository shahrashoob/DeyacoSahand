<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToShiftWorkDays extends Migration
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
            $table->foreignId( "work_day_type_id" )->comment( "نوع روز کاری: 1- روز کاری 2- تعطیلی رسمی 3- تعطیلی غیررسمی" );
            $table->integer( "legal_working_hours_in_minute" )->comment( "ساعت کار قانونی (دقیقه)" );
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
