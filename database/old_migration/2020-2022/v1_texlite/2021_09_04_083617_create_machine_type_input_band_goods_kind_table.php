<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTypeInputBandGoodsKindTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_type_input_band_goods_kind', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_input_band_id")->comment("باند ورودی نوع ماشین");
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
        Schema::dropIfExists('machine_type_input_band_goods_kind');
    }
}
