<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeforeAmountToMachineAllocationModificationPackingForm extends Migration
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
            $table->double("before_amount")->nullable()->comment("مقدار سیستمی بسته بندی قبل از برگشت مواد اولیه");
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
