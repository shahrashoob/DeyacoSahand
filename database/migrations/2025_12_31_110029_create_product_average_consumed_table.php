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
        Schema::create('consumed_product_average', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->dateTime('month');
            $table->float('average_amount')->comment("میانگین مصرف کالا در یک ماه");
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumed_product_average');
    }
};
