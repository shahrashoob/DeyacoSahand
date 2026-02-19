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
        Schema::create('product_fault_product_fault_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_fault_id");
            $table->foreignId("product_fault_property_id");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `product_fault_product_fault_properties` comment 'نگهداری اطلاعات مشخصه های نقص'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_fault_property_value');
    }
};
