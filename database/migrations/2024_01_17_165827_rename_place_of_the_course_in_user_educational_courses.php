<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePlaceOfTheCourseInUserEducationalCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_educational_courses', function (Blueprint $table) {
            $table->renameColumn('place_of_the_course', 'name_of_institution');
            $table->renameColumn('date_of_the_course', 'start_date')->comment('تاریخ شروع دوره');
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
