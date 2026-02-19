<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTimeInMinuteToTimeInSecondsFormReport1010MachingLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_1010_machine_logs', function (Blueprint $table) {
            //
            $table->integer("time_in_minute")->comment("زمان به ثانیه")->change();
            $table->renameColumn("time_in_minute","time_in_seconds");

            $table->float("theory_contour")->change();
            $table->float("operation_contour")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('minute_to_time_in_seconds_form_report_1010_maching_logs', function (Blueprint $table) {
            //
        });
    }
}
