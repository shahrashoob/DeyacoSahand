<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMachineLogIdFromMachineAllocationMaterialConsumed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation_material_consumed', function (Blueprint $table) {
            //
            $table->foreignId("start_machine_log_id")->nullable()->change();
            $table->foreignId("end_machine_log_id")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation_material_consumed', function (Blueprint $table) {
            //
        });
    }
}
