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
        Schema::create('producton_place_of_issue', function (Blueprint $table) {
            $table->id();
            // دستور تولید ویژه دوره پیاده سازی
            $table->integer('in_implementation')->nullable;
            // در سفارش توسط اپراتور
            $table->integer('in_order_by_user')->nullable;
            //در سفارش توسط کارشناس دیجیتال دیاکو
            $table->integer('in_order_by_deyaco_script')->nullable;
            //کارشناس دیجیتال دیاکو
            $table->integer('by_deyaco_script')->nullable;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producton_place_of_issue');
    }
};
