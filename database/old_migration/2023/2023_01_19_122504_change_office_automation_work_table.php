<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOfficeAutomationWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('office_automation_works', function (Blueprint $table) {
            //
           $table->dropColumn("office_automation_work_parent_id");
           $table->dropColumn("office_automation_to_do_list_parent_id");
           $table->dropColumn("description");
           $table->dropColumn("start_datetime");
           $table->dropColumn("office_automation_to_do_type_id");
        });
        DB::statement("ALTER TABLE `office_automation_works` comment 'جدول لیست کارها'");

        Schema::table('office_automation_to_do_list', function (Blueprint $table) {
            //
            $table->dropColumn("office_automation_to_do_type_id");
            $table->dropColumn("post_id");

            $table->text("description")->after("status_id")->nullable();
            $table->string("caption")->after("status_id")->comment("عنوان ارجاع");
            $table->dateTime("end_datetime")->after("status_id")->comment("تاریخ پایان ارجاع");
            $table->foreignId("priority_id")->after("status_id")->comment("اولویت");
        });

        DB::statement("ALTER TABLE `office_automation_to_do_list` comment 'جدول لیست ارجاع ها'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
