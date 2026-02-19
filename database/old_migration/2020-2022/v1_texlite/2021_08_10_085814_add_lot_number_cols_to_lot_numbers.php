<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotNumberColsToLotNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lot_numbers', function (Blueprint $table) {
            //
            $table->foreignId("machine_lot_effective_code")->nullable()->comment("لات موثر ماشین");

            $table->integer("lot_effective_code1")->nullable()->comment("لات موثر 1");
            $table->integer("lot_effective_code2")->nullable()->comment("لات موثر 2");
            $table->integer("lot_effective_code3")->nullable()->comment("لات موثر 3");

            $table->foreignId("lot_number_1_id")->nullable();

            $table->foreignId("lot_number_2_id")->nullable();

            $table->foreignId("lot_number_3_id")->nullable();

            $table->foreignId("lot_number_4_id")->nullable();

            $table->foreignId("lot_number_5_id")->nullable();

            $table->foreignId("lot_number_6_id")->nullable();

            $table->foreignId("lot_number_7_id")->nullable();

            $table->foreignId("lot_number_8_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lot_numbers', function (Blueprint $table) {
            //
        });
    }
}
