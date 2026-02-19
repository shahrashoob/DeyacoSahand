<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineContourUnitIdToMachineTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
            $table->foreignId("machine_contour_unit_id")->nullable()->comment("واحد شمارنده کنتور های ماشین");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
        });
    }
}
