<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityIdToOfficeAutomationWorks extends Migration
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
            $table->foreignId("priority_id")->after("parent_id")->default(201)->comment("اولویت ها (نوع 2)");
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
