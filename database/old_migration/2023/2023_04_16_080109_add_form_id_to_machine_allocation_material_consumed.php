<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormIdToMachineAllocationMaterialConsumed extends Migration
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
            $table->foreignId("form_id")->nullable()->comment("برگ خروج که به ازای مصرف ثبت گردید است.");
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
