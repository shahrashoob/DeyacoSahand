<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeGrossWeightFromModificationPackingForm extends Migration
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
            $table->float("gross_weight",15,7)->nullable()->change();
            $table->integer("sub_packing_form_number")->nullable()->change();
            $table->integer("amount")->nullable()->change();
            $table->integer("weight")->nullable()->change();

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
