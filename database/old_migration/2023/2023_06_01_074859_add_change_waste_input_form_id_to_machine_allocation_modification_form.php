<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeWasteInputFormIdToMachineAllocationModificationForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation_modification_form', function (Blueprint $table) {
            //
            $table->foreignId("change_waste_input_form_id")->nullable()->comment("فرم ورود تراکنش های ضایعات");
            $table->foreignId("change_waste_output_form_id")->nullable()->comment("فرم خروج تراکنش های ضایعات");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation_modification_form', function (Blueprint $table) {
            //
        });
    }
}
