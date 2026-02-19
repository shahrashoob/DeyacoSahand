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
            $table->integer('in_implementation')->default(1)->comment('دستور تولید ویژه دوره پیاده سازی');
            $table->integer('in_order_by_user')->default(1)->comment('در سفارش توسط اپراتور');
            $table->integer('in_order_by_diaco_script')->default(0)->comment('در سفارش توسط کارشناسان دیجیتال دیاکو');
            $table->integer('by_deyaco_script')->default(0)->comment('کارشناس دیجیتال دیاکو');
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
