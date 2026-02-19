<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeAutomationWorkParentIdToOfficeAutomationWorks extends Migration
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
            $table->foreignId("office_automation_work_parent_id")->nullable()->
            after("status_id")->comment("ممکن است یک کار حاصل اجرای ماژول کار جدید داخل یک کار پدر باشد، این شناسه کار پدر است. ");
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
