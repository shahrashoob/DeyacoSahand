<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceAfterPackingTypeIdToMachineAllocationActualCosts extends Migration
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
            $table->double("price", 15, 2)->comment("قیمت");
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
