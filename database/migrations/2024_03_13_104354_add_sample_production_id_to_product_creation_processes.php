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
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
            $table->foreignId("sample_production_id")->nullable()->comment("کارت تولید نمونه گیری");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
        });
    }
};
