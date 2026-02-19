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
        Schema::create('goods_kind_lot_number_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lot_number_property_id")->index();
            $table->foreignId("goods_kind_id")->index();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `goods_kind_lot_number_properties` comment 'هر مشخصه لات می تواند متعلق به یک یا چند رسته کالایی باشد'");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_kind_lot_number_properties');
    }
};
