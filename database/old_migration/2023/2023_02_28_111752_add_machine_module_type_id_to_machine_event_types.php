<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineModuleTypeIdToMachineEventTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_event_types', function (Blueprint $table) {
            //
            $table->foreignId("machine_module_type_id")->comment("نوع ماژول ماشین");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_event_types', function (Blueprint $table) {
            //
        });
    }
}
