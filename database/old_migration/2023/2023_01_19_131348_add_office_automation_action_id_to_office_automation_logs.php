<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeAutomationActionIdToOfficeAutomationLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('office_automation_logs', function (Blueprint $table) {
            //
            $table->foreignId("office_automation_action_id")->nullable();
            $table->foreignId("office_automation_action_status_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('office_automation_logs', function (Blueprint $table) {
            //
        });
    }
}
