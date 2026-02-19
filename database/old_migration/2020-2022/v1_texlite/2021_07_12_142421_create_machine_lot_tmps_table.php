<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineLotTmpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // جدول موقت برای ذخیره لات های نخ و چله ماشین برای تشخیص لات پارچه خام
        Schema::create('machine_lot_tmps', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_id");

            $table->foreignId("warps_lot_number_id")->nullable();

            $table->foreignId("yarn_id_1")->nullable();
            $table->foreignId("yarn1_lot_number_id")->nullable();

            $table->foreignId("yarn_id_2")->nullable();
            $table->foreignId("yarn2_lot_number_id")->nullable();

            $table->foreignId("yarn_id_3")->nullable();
            $table->foreignId("yarn3_lot_number_id")->nullable();

            $table->foreignId("yarn_id_4")->nullable();
            $table->foreignId("yarn4_lot_number_id")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_lot_tmps');
    }
}
