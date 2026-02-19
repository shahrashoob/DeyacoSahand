<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputOutputFormsToMachineAllocationModificationForm extends Migration
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
            $table->foreignId("amendment_input_form_id")->nullable()->comment("فرم ورود تراکنش اصلاحی");
            $table->foreignId("amendment_output_form_id")->nullable()->comment("فرم خروج تراکنش اصلاحی");
            $table->foreignId("degree_change_input_form_id")->nullable()->comment("فرم وورد تغییر درجه");
            $table->foreignId("degree_change_output_form_id")->nullable()->comment("فرم خروج تغییر درجه");
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
