<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReanmeParentIdInOfficeAutomationWorks extends Migration
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
            $table->renameColumn("parent_id","office_automation_to_do_list_parent_id");

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
