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
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->integer("is_need_for_quality_control")->default(0)->comment("آیا بعد از انجام عملیات نیاز به تایید کنترل کیفیت می باشد؟");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
        });
    }
};
