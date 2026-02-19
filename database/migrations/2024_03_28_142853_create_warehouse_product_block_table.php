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
        Schema::create('warehouse_product_block', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_id")->index();
            $table->foreignId("product_id")->nullable()->index()->comment("اگر کالا نال باشد یعنی همه کالاها");
            $table->foreignId("warehouse_handling_id")->index();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `warehouse_product_block` comment 'در این جدول کالاهایی که تراکنش انبار برای آنها غیر مجاز است ثبت می گردد.'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_product_block');
    }
};
