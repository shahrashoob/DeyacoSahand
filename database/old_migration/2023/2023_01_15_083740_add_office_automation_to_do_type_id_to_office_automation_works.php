<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeAutomationToDoTypeIdToOfficeAutomationWorks extends Migration
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
            $table->foreignId("office_automation_to_do_type_id")->after("priority_id")->default(2)->comment("نوع کار (دستور، ابلاغ و ...)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('do_type_id_to_office_automation_works', function (Blueprint $table) {
            //
        });
    }
}
