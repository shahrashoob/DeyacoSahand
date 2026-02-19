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
        Schema::table('current_machine_input_logs', function (Blueprint $table) {
            //
            $table->index('current_machine_input_id');
            $table->index('machine_id');
            $table->index('allocation_id');
            $table->index('production_id');
            $table->index('production_form_id');
            $table->index('product_id');
            $table->index('material_id');
            $table->index('lot_number_id');
            $table->index('entry_packing_form_id');
            $table->index('packing_form_id');
            $table->index('user_id');
            $table->index('machine_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('current_machine_input_logs', function (Blueprint $table) {
            //
        });
    }
};
