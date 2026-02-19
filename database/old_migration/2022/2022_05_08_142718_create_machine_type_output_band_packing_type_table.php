<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTypeOutputBandPackingTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_type_output_band_packing_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_output_band_id");
            $table->foreignId("goods_kind_id");
            $table->foreignId("machine_type_id");
            $table->foreignId("packing_type_id");
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
        Schema::dropIfExists('machine_type_output_band_packing_type');
    }
}
