<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartAndEndDatetimeToUserAcademicDegree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_academic_degree', function (Blueprint $table) {
            //
            $table->date("start_date")->comment("تاریخ شروع به تحضیل");
            $table->date("end_date")->comment("تاریخ پایان به تحضیل");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_academic_degree', function (Blueprint $table) {
            //
        });
    }
}
