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
        Schema::table('current_machine_material_flows', function (Blueprint $table) {
            //
            $table->index('allocation_id');
            $table->index('product_id');
            $table->index('material_id');
            $table->index('input_band_id');
            $table->index('goods_kind_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('current_machine_material_flows', function (Blueprint $table) {
            //
        });
    }
};
