<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPredictColsToAllocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allocations', function (Blueprint $table) {
            //
            $table->integer("predict_of_production_time_practical")->nullable()->comment("پیش بینی زمان تولید(عملی) - دقیقه");
            $table->integer("predict_of_production_time_theory")->nullable()->comment("پیش بینی زمان تولید(تثوری) - دقیقه");

            $table->dateTime("predict_of_production_start_date_practical")->nullable()->comment("پیش بینی تاریخ شروع تولید (عملی)");
            $table->dateTime("predict_of_production_start_date_practical_updated")->nullable()->comment("پیش بینی تاریخ شروع تولید (عملی) - بروز شده");

            $table->dateTime("predict_of_production_start_date_theory")->nullable()->comment("پیش بینی تاریخ شروع تولید (تثوری) ");
            $table->dateTime("predict_of_production_start_date_theory_updated")->nullable()->comment("پیش بینی تاریخ شروع تولید (تثوری) - بروز شده");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allocations', function (Blueprint $table) {
            //
        });
    }
}
