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
        Schema::create('station_station_operation_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId("station_id");
            $table->foreignId("station_operation_category_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `station_station_operation_category` comment 'در این جدول به ازای هر ایستگاه کاری مشخص می کنیم که شامل چه دسته عملیات هایی است.'");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_station_operation_category');
    }
};
