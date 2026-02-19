<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndDateDurationToUserEducationalCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_educational_courses', function (Blueprint $table) {
            $table->integer("duration")->nullable()->comment('مدت دوره');
            $table->date("end_date")->nullable()->comment("تاریخ پایان دوره");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_educational_courses', function (Blueprint $table) {
            //
        });
    }
}
