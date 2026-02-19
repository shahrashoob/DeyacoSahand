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
        Schema::create('product_pricing_product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_pricing_log_id")->comment("لاگ مربوط به بارگذاری لیست قیمت");
            $table->foreignId("product_id")->index();
            $table->foreignId("packing_type_id")->index();
            $table->double("price",15,2)->comment("قیمت بروز (ریال)");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `product_pricing_product_logs` comment 'لیست سابقه قیمت گذاری کالا'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_pricing_product_logs');
    }
};
