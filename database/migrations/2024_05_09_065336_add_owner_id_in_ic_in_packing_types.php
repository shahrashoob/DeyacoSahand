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
        Schema::table('packing_types', function (Blueprint $table) {
            $table->foreignId('owner_personal_id_in_ic')->nullable();
            $table->foreignId('definer_personal_id_in_ic')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_types', function (Blueprint $table) {
            //
        });
    }
};
