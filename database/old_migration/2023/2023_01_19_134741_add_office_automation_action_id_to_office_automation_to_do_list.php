<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeAutomationActionIdToOfficeAutomationToDoList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('office_automation_to_do_list', function (Blueprint $table) {
            //
            $table->foreignId("office_automation_action_id")->after("office_automation_work_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('office_automation_to_do_list', function (Blueprint $table) {
            //
        });
    }
}
