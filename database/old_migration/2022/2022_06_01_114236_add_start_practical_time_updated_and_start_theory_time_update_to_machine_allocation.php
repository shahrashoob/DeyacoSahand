<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartPracticalTimeUpdatedAndStartTheoryTimeUpdateToMachineAllocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
            $table->dateTime("start_practical_time_updated")->nullable()->after("start_practical_time")->comment("زمان پیش بینی شروع تولید بروزرسانی شده(عملی)");
            $table->dateTime("start_theory_time_updated")->nullable()->after("start_theory_time")->comment("زمان پیش بینی شروع تولید بروزرسانی شده(تثوری)");
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
