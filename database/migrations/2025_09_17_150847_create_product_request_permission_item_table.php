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
        Schema::create('product_request_permission_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_request_permission_id')->index("prpi_prp_id");
            $table->foreignId('product_request_form_id')->index("prpi_prf_id");
            $table->foreignId('order_id')->index("prpi_order_id");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `product_request_permission_items` comment 'جدول آیتم های مجوزهای بارگیری'");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_request_permission_item');
    }
};
