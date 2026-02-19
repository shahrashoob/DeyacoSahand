<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEarlierOrDelayTimeToPosts extends Migration
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
            $table->integer("allowed_earlier_time_for_entry")->default(0)->comment("تعجیل مجاز برای ورود به سازمان (دقیقه)");
            $table->integer("allowed_delay_time_for_entry")->default(0)->comment("تاخیر مجاز برای ورود به سازمان (دقیقه)");
            $table->integer("allowed_earlier_time_for_exit")->default(0)->comment("تعجیل مجاز برای خروج به سازمان (دقیقه)");
            $table->integer("allowed_delay_time_for_exit")->default(0)->comment("تاخیر مجاز برای خروج به سازمان (دقیقه)");
            $table->integer("should_complete_the_duration_of_operation")->default(0)->comment("آیا باید مدت زمان کارکرد را تکمیل نماید.");
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
