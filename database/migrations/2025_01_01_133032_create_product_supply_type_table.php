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
        Schema::create('product_supply_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->index();
            $table->foreignId("supply_type_id")->index();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `product_supply_type` comment 'یک کالا می تواند بیش از یک نوع تامین داشته باشد.'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supply_type');
    }
};
