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
        Schema::table('current_machine_faults', function (Blueprint $table) {
            //
            $table->index('machine_id');
            $table->index('user_id');
            $table->index('machine_fault_id');
            $table->index('active_status_id');
            $table->index('status_id');
            $table->index('maintenance_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('current_machine_faults', function (Blueprint $table) {
            //
        });
    }
};
