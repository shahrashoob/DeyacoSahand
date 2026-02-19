<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStationIdAndWarehouseIdToNewBillOfMaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_bill_of_material', function (Blueprint $table) {
            //
            $table->string("station_id");
            $table->string("station_code");
            $table->string("warehouse_code");
            $table->string("warehouse_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_bill_of_material', function (Blueprint $table) {
            //
        });
    }
}
