<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWeightFromMachineAllocationModificationPackingForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation_modification_packing_form', function (Blueprint $table) {
            //
            $table->float("weight")->change();
            $table->float("amount")->change();
            $table->float("before_amount")->change();
            $table->float("before_gross_weight")->change();
            $table->float("before_weight")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation_modification_packing_form', function (Blueprint $table) {
            //
        });
    }
}
