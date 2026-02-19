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
        Schema::create('warehouse_shelving_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_id");
            $table->foreignId("warehouse_shelving_id");
            $table->foreignId("product_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_shelving_product');
    }
};
