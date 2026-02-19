<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserAcademic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_academic_degree', function (Blueprint $table) {

            $table->foreignId('academic_degree_type_id')->nullable()->comment('مقطع تحصیلی');
            $table->string('name_of_academic_degree')->nullable()->comment('نام موسسه تحصیل');
            $table->string('feild_of_academic_degree')->nullable()->comment('رشته تحصیل');
            $table->foreignId('academic_degree_file_id')->nullable()->comment('مدرک تحصیل');
            $table->float('average')->nullable()->comment('معدل');

            $table->removeColumn("academic_degree_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
