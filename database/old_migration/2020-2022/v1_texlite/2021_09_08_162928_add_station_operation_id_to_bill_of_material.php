<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStationOperationIdToBillOfMaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material', function (Blueprint $table) {
            //
            $table->foreignId("station_operation_id")->default(1)->comment("نوع عملیات مربوطه");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_material', function (Blueprint $table) {
            //
        });
    }
}
