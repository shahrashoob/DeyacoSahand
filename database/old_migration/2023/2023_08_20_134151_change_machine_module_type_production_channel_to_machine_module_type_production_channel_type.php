<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMachineModuleTypeProductionChannelToMachineModuleTypeProductionChannelType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_module_type_production_channel', function (Blueprint $table) {
            //
            $table->rename("machine_module_type_production_channel_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_module_type_production_channel_type', function (Blueprint $table) {
            //
        });
    }
}
