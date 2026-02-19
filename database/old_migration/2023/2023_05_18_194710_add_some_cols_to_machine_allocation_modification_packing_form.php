<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToMachineAllocationModificationPackingForm extends Migration
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
            $table->integer("before_sub_packing_form_number")->nullable()->comment("تعداد بسته بندی قبل از برگشت به انبار");
            $table->integer("before_gross_weight")->nullable()->comment("وزن ناخالص قبل از برگشت به انبار");
            $table->integer("before_weight")->nullable()->comment("وزن خالص قبل از برگشت به انبار");
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
