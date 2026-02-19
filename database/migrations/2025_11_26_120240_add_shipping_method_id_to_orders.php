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
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->foreignId('shipping_method_id')->nullable()->index()->comment("نوع ارسال بار");
            $table->foreignId('car_type_id')->nullable()->index()->comment("نوع خودرو");
            $table->integer('insurance_amount')->nullable()->index()->comment("مبلغ بیمه نامه");

            $table->foreignId('delivery_point_type_id')->nullable()->index()->comment("محل تحویل بار");
            $table->integer('shipping_cost')->nullable()->comment("هزینه ارسال بار");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
