<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsFromMachineAllocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            $table->integer("predict_of_production_time_practical")->comment("پیش بینی زمان تولید(عملی)")->change();
            $table->integer( "predict_of_production_time_theory" )->comment( "پیش بینی زمان تولید(تثوری)" )->change();

            $table->dateTime( "predict_of_production_start_date_practical" )->comment( "پیش بینی تاریخ شروع تولید (عملی)" )->change();
            $table->dateTime( "predict_of_production_start_date_theory" )->comment( "پیش بینی تاریخ شروع تولید (تثوری)" )->change();

            $table->dateTime( "predict_of_production_start_date_practical_updated" )->comment( "پیش بینی تاریخ شروع تولید (عملی) - بروز شده" )->change();
            $table->dateTime( "predict_of_production_start_date_theory_updated" )->comment( "پیش بینی تاریخ شروع تولید (تثوری)  - بروز شده" )->change();

            $table->dateTime( "production_start_date" )->comment( " تاریخ شروع به تولید" )->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
        });
    }
}
