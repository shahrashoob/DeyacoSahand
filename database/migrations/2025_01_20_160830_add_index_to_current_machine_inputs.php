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
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
            $table->index('production_id');
            $table->index('product_id');
            $table->index('lot_number_id');
            $table->index('machine_id');
            $table->index('input_band_id');
            $table->index('allocation_id');
            $table->index('goods_kind_id');
            $table->index('packing_form_id');
            $table->index('consume_warehouse_id');
            $table->index('request_warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
        });
    }
};
