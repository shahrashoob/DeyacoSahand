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
        Schema::table('machine_type_output_bands', function (Blueprint $table) {
            //
            $table->foreignId('doff_algorithm_id')->default(601);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_type_output_bands', function (Blueprint $table) {
            //
        });
    }
};
