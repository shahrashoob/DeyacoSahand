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
        Schema::create('product_pricing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("message_id");
            $table->foreignId("user_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `product_pricing_logs` comment 'لاگ بارگذاری لیست قیمت گذاری'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_pricing_log');
    }
};
