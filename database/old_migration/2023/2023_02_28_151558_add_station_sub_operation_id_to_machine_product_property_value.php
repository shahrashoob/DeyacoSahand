<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStationSubOperationIdToMachineProductPropertyValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_product_property_value', function (Blueprint $table) {
            //
            $table->foreignId("station_sub_operation_id")->after("station_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_product_property_value', function (Blueprint $table) {
            //
        });
    }
}
