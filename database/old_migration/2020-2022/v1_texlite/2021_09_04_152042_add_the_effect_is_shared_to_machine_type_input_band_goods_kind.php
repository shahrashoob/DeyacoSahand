<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTheEffectIsSharedToMachineTypeInputBandGoodsKind extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_type_input_band_goods_kind', function (Blueprint $table) {
            //
            $table->boolean("effect_is_shared")->default(true)->comment("آیا تاثیر بر همبافت به صورت اشتراکی است؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_type_input_band_goods_kind', function (Blueprint $table) {
            //
        });
    }
}
