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
        Schema::create('warehouse_handling_goods_kind_time_limit', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_type_id")->index();
            $table->foreignId("belonging_to_id")->index();
            $table->foreignId("goods_kind_id")->index();
            $table->integer("warehouse_handling_time_limit")->default("60")->comment("مدت زمان انبارگردانی برای رسته کالایی برای هر نوع انبار");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `warehouse_handling_goods_kind_time_limit` comment 'به ازای هر نوع انبار و هر رسته کالایی مدت زمان انبارگردانی را در این جدول ذخیره می کنیم.'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_handling_goods_kind_time_limit');
    }
};
