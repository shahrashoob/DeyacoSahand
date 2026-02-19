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
        Schema::table('new_tariff_product', function (Blueprint $table) {
            //
            $table->string('customer_product_caption')->nullable()->comment('نام  کالای مشتری');
            $table->string('customer_product_code')->nullable()->comment('کد کالای مشتری');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_tariff_product', function (Blueprint $table) {
            //
        });
    }
};
