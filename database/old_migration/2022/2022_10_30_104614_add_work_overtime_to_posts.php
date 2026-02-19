<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkOvertimeToPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
            $table->integer("for_work_overtime_a_few_top_levels_must_confirm")->default(1)->comment("برای اضافه کاری باید چند سطح بالایی تایید کنند");
            $table->integer("for_work_overtime_a_few_top_levels_must_confirm_time")->default(1)->comment("برای اضافه کاری بیش از x ساعت باید چند سطح بالایی تایید کنند");
            $table->integer("for_work_overtime_a_few_top_levels_must_confirm_time_level")->default(1)->comment("برای اضافه کاری بیش از x ساعت باید چند سطح بالایی تایید کنند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
        });
    }
}
