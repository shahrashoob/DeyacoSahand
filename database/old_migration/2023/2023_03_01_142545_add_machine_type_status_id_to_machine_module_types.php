<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineTypeStatusIdToMachineModuleTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_module_types', function (Blueprint $table) {
            //
            $table->foreignId("machine_status_type_id")->comment("نوع وضعیتی که برای وضعیت های ماشین در جدول status_types در نظر گرفته شده است.");
            $table->foreignId("production_status_type_id")->comment("نوع وضعیتی که برای وضعیت های کارت تولید در جدول status_types در نظر گرفته شده است.");
            $table->foreignId("production_form_status_type_id")->comment("نوع وضعیتی که برای وضعیت های فرم تولید در جدول status_types در نظر گرفته شده است.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_module_types', function (Blueprint $table) {
            //
        });
    }
}
