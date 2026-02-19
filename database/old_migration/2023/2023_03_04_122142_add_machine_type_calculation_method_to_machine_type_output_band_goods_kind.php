<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineTypeCalculationMethodToMachineTypeOutputBandGoodsKind extends Migration
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
            $table->foreignId("machine_type_calculation_method_for_unit_id")->default(1)->comment("روش محاسبه مقدار اصلی کالا در رسته کالایی");
            $table->foreignId("machine_type_calculation_method_for_sub_unit_id")->default(1)->comment("روش محاسبه مقدار فرعی کالا در رسته کالایی");
            $table->foreignId("machine_type_calculation_method_for_sub_unit2_id")->default(1)->comment("روش محاسبه مقدار فرعی 2 کالا در رسته کالایی");
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
