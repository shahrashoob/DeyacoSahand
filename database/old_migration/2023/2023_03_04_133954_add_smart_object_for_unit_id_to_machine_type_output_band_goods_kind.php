<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSmartObjectForUnitIdToMachineTypeOutputBandGoodsKind extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_type_output_band_goods_kind', function (Blueprint $table) {
            //
            $table->foreignId("smart_object_id_for_unit")->nullable()->comment("شیئ انتخاب شده برای مقدار اصلی کالا");
            $table->foreignId("smart_object_id_for_sub_unit")->nullable()->comment("شیئ انتخاب شده برای مقدار فرعی کالا");
            $table->foreignId("smart_object_id_for_sub_unit2")->nullable()->comment("شیئ انتخاب شده برای مقدار فرعی 2 کالا");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_type_output_band_goods_kind', function (Blueprint $table) {
            //
        });
    }
}
