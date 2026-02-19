<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('machine_type_output_band_goods_kind', function (Blueprint $table) {
            //
            $table->index('machine_type_output_band_id',"mtobgk_1");
            $table->index('goods_kind_id');
            $table->index('machine_type_calculation_method_for_unit_id',"mtobgk_2");
            $table->index('machine_type_calculation_method_for_sub_unit_id',"mtobgk_3");
            $table->index('machine_type_calculation_method_for_sub_unit2_id','"mtobgk_4"');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_type_output_band_goods_kind', function (Blueprint $table) {
            //
        });
    }
};
