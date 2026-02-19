<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineModuleTypeIdToMachineTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
            $table->foreignId("machine_module_type_id")->comment("نوع دسته ماژول هایی که باید برای تغییر وضعیت های ماشین اجرا شود.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
        });
    }
}
