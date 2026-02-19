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
        Schema::create('bill_of_material_item_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_of_material_log_id');
            $table->foreignId('bill_of_material_id');
            $table->foreignId('bill_of_material_item_id')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->foreignId('material_id')->nullable();

            $table->double('amount',15,8)->nullable();
            $table->integer('number')->nullable();
            $table->integer('percent_of_use')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_material_item_log');
    }
};
