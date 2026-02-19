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
        Schema::create('bill_of_material_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id');
            $table->foreignId('bill_of_material_id');

            $table->foreignId('production_id')->nullable();
            $table->foreignId('allocation_id')->nullable();
            $table->foreignId("user_id");

            $table->integer("version");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_material_logs');
    }
};
