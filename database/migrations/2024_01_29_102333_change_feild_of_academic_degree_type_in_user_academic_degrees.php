<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFeildOfAcademicDegreeTypeInUserAcademicDegrees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_academic_degrees', function (Blueprint $table) {
            $table->foreignId('feild_of_academic_degree_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_academic_degrees', function (Blueprint $table) {
            //
        });
    }
}
