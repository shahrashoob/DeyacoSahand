<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPracticalAndTheoryTimeToMachineAllocation extends Migration
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
            $table->integer("practical_time")->nullable()->comment("زمان عملی تولید به دقیقه");
            $table->integer("theory_time")->nullable()->comment("زمان تثوری تولید به دقیقه");
            $table->dateTime("start_practical_time")->nullable()->comment("تاریخ شروغ عملی تولید");
            $table->dateTime("start_theory_time")->nullable()->comment("تاریخ شورع تثوری تولید ");
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
