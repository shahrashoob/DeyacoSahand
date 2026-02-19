<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBeforeAmountInMachineAllocationModificationPackingForm extends Migration
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

        });
        DB::statement("ALTER TABLE `machine_allocation_modification_packing_form` 	CHANGE COLUMN `before_amount` `before_amount` DOUBLE(15,6) NULL");

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
