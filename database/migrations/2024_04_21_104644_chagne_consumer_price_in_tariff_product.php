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
        Schema::table('product_tariff', function (Blueprint $table) {
            //
            $table->integer("consumer_price")->nullable()->change();
        });
        Schema::table('product_tariff_logs', function (Blueprint $table) {
            //
            $table->integer("consumer_price")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tariff_product', function (Blueprint $table) {
            //
        });
    }
};
