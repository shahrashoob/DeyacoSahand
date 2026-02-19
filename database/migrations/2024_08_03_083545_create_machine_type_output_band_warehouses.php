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
        Schema::create('machine_type_output_band_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_id")->index();
            $table->foreignId("goods_kind_id")->index();
            $table->foreignId("degree_id")->index();
            $table->foreignId("warehouse_id")->index();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `machine_type_output_band_warehouses` comment 'انبار هر فرم های خروجی به ازای هر رسته کالایی و درجه و گروه ماشین را مشخص می کنیم.'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_type_output_band_warehouses');
    }
};
