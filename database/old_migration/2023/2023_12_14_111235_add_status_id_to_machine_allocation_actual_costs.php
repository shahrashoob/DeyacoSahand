<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToMachineAllocationActualCosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation_actual_costs', function (Blueprint $table) {
            //
            $table->foreignId("status_id")->comment("وضعیت محاسبه قیمت تمام شده");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation_actual_costs', function (Blueprint $table) {
            //
        });
    }
}
