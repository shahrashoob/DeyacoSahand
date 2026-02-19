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
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
            $table->foreignId("property1_id")->index()->nullable()->comment("مشخصه اصلی در رسته کالایی");
            $table->foreignId("property2_id")->index()->nullable()->comment("مشخصه فرعی در رسته کالایی");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
        });
    }
};
