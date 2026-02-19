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
        Schema::table('machine_type_output_band_warehouses', function (Blueprint $table) {
            //
            $table->foreignId('quality_control_warehouse_id')->default(null)->comment(" انبار کنترل کیفیت");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_type_output_band_warehouses', function (Blueprint $table) {
            //
        });
    }
};
