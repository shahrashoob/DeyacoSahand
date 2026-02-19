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
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
            $table->index('production_id');
            $table->index('machine_id');
            $table->index('contractor_id');
            $table->index('supplier_id');
            $table->index('status_id');
            $table->index('product_id');
            $table->index('allocation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
        });
    }
};
