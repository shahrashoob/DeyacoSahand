<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEducationalCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_educational_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->string('course_name')->comment('نام دوره');
            $table->string('place_of_the_course')->comment('محل دوره');
            $table->date('date_of_the_course')->comment('تاریخ دوره');
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
        Schema::dropIfExists('user_educational_courses');
    }
}
