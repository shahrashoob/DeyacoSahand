<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingFormIdToCurrentMachineInput extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
            $table->foreignId("packing_form_id")->nullable()->comment("بسته بندی جاری که توسط اپراتور وارد شده است.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('current_machine_input', function (Blueprint $table) {
            //
        });
    }
}
