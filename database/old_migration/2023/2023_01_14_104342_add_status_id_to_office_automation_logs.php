<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToOfficeAutomationLogs extends Migration
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
            $table->foreignId("office_automation_work_status_id")->after("event_id");
            $table->foreignId("office_automation_to_do_list_status_id")->after("event_id")->nullable();
            $table->dropColumn("status_id");
            $table->foreignId("office_automation_to_do_list_id")->nullable()->change();
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
