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
        Schema::create('product_tariff_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tariff_id");
            $table->foreignId("product_id");
            $table->foreignId("degree_id");
            $table->foreignId("warehouse_id");
            $table->foreignId("packing_type_id");
            $table->foreignId("type_of_sale_of_product_id");
            $table->integer("min_buy");
            $table->integer("max_buy");
            $table->integer("tax");
            $table->integer("fare");
            $table->integer("fea")->nullable();
            $table->integer("consumer_price")->nullable();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `product_tariff_pricing` comment 'در این جدول ردیف های تعرفه جدید که قرار است به لیست تعرفه اضافه شود، به صورت موقت نگهداری می شود.'");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tariff_pricing');
    }
};
