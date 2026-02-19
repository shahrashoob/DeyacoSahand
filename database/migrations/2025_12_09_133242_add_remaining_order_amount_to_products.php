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
        Schema::table('products', function (Blueprint $table) {
            //
            $table->float('remaining_order_amount')->comment("مقدار باقی مانده سفارشات ")->nullable();
            $table->float('current_order_needed_amount')->comment("مقدار لازم جهت سفارشات جاری ")->nullable();
            $table->dateTime('last_ran_planing_algorithm')->comment("آخرین زمان بروز رسانی الگوریتم برنامه ریزی تولید")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
