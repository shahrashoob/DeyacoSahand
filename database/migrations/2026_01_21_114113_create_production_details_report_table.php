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
        Schema::create('production_details_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("production_id");
            $table->foreignId("product_id");

            $table->double("order_amount")->nullable()->comment(" مقدار سفارش کارت تولید (خودش قابلیت فورش دارد، یا سطح بالا)");
            $table->double("production_amount")->comment(" مقدار کارت تولید");
            $table->double("allocation_amount")->comment(" مقدار تخصیص");
            $table->double("production_form_amount")->comment(" مقدار تولید شده");

            $table->foreignId("parent_product_id")->nullable();
            $table->foreignId("parent_production_id")->nullable();

            $table->double("parent_production_amount")->nullable()->comment(" مقدار کارت تولید");
            $table->double("parent_allocation_amount")->nullable()->comment(" مقدار تخصیص");
            $table->double("parent_production_form_amount")->nullable()->comment(" مقدار تولید شده");



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_details_report');
    }
};
