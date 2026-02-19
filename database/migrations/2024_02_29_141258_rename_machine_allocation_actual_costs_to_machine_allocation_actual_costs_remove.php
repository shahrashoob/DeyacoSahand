<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('machine_allocation_actual_costs', function (Blueprint $table) {
            //
            $table->rename("machine_allocation_actual_costs_remove");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation_actual_costs_remove', function (Blueprint $table) {
            //
        });
    }
};
