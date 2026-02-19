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
        Schema::table('machine_allocation_logs', function (Blueprint $table) {
            //
            $table->foreignId("line_product_station_id")->nullable()->comment("مسیر محصول جاری");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation_logs', function (Blueprint $table) {
            //
        });
    }
};
