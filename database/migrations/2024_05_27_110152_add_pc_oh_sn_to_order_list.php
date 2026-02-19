<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_list', function (Blueprint $table) {
            //
            $table->string("destination_product_code")->comment("کد کالای مقصد ");
            $table->string("tracking_code1")->comment("کد پیگیری 1 (شماره سفارش) ");
            $table->string("tracking_code2")->comment(" کد پیگیری 2 (دستور پیمان) ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_list', function (Blueprint $table) {
            //
        });
    }
};
