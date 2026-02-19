<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherCorrectionToMachineAllocationModificationForm extends Migration
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
            $table->foreignId("other_correction_input_form_id")->nullable()->comment("فرم ورود دیگر تراکنش های اصلاحی (مثل حذف رقم اعشار و ...)");
            $table->foreignId("other_correction_output_form_id")->nullable()->comment("فرم خروج دیگر تراکنش های اصلاحی (مثل حذف رقم اعشار و ...)");
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
