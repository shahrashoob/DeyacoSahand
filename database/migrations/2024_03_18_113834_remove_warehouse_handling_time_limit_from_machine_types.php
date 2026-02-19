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
        Schema::table('machine_types', function (Blueprint $table) {
            //
            $table->dropColumn("warehouse_handling_time_limit");
        });
        Schema::table('stations', function (Blueprint $table) {
            //
            $table->dropColumn("warehouse_handling_time_limit");
        });
        Schema::table('lines', function (Blueprint $table) {
            //
            $table->dropColumn("warehouse_handling_time_limit");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
        });
    }
};
