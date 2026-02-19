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
        Schema::table('product_request_permissions', function (Blueprint $table) {
            //
            $table->foreignId('address_id');
            $table->foreignId('shipping_method_id')->comment("نوع ارسال بار");
            $table->foreignId('car_type_id')->comment("نوع خودرو");
            $table->integer('insurance_amount')->comment("مبلع بیمه نامه");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_permissions', function (Blueprint $table) {
            //
        });
    }
};
