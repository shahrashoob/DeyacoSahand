<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineProductionStatusIdAndMachineOffReasonToMachineModuleTypes extends Migration
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
            $table->foreignId("machine_production_status_id")->comment(" وضعیت تولید ماشین (پیش فرض)");
            $table->foreignId("machine_off_reason")->comment(" دلیل خاموش بودن ماشین (پیش فرض)");
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
