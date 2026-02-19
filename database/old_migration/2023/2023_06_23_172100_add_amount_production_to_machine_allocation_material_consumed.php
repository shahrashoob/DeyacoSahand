<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountProductionToMachineAllocationMaterialConsumed extends Migration
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
            $table->double("amount_production",15,4)->nullable()->
            comment("مقدار تولید شده که باید برای آن تراکنش مصرف ثبت شود، برای ماشین هایی که روش ثبت مصرف آنها بر اساس مقدار تولید است.");
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
