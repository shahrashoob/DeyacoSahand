<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToLineProductStation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->integer("setup_time_for_co_channel")->nullable()->comment("زمان ستاب هم کانال");
            $table->integer("setup_time_for_non_co_channel")->nullable()->comment("زمان ستاب غیر هم کانال");
            $table->integer("setup_time_for_sub_operation")->nullable()->comment("زمان  عملیات فرعی");
            $table->integer("setup_time_for_final_setting")->nullable()->comment("زمان تنظیمات پایانی");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
        });
    }
}
