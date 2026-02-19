<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTypeOutputBandGoodsKindTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_type_output_band_goods_kind', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_output_band_id")->comment("باند خروجی نوع ماشین");
            $table->foreignId("goods_kind_id");
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
        Schema::dropIfExists('machine_type_output_band_goods_kind');
    }
}
