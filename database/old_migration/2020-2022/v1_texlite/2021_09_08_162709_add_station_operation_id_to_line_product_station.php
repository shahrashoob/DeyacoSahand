<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStationOperationIdToLineProductStation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_product_station', function (Blueprint $table) {
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
        Schema::table('line_product_station', function (Blueprint $table) {
            //
        });
    }
}
