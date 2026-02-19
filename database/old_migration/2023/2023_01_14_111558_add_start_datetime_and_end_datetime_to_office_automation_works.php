<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartDatetimeAndEndDatetimeToOfficeAutomationWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('office_automation_works', function (Blueprint $table) {
            //
            $table->dateTime("start_datetime");
            $table->dateTime("end_datetime");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('office_automation_works', function (Blueprint $table) {
            //
        });
    }
}
