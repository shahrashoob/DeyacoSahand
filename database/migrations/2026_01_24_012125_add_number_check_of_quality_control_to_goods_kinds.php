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
            $table->integer('number_check_of_quality_control_input_warehouse', )->default(3)->comment("تعداد مورد نیاز جهت تایید در زمان کنترل تخلیه بار");
            $table->integer('percent_check_of_quality_control_input_warehouse', )->default(5)->comment("درصد مجاز اختلاف بین گرماژ کالا در زمان کنترل تخلیه بار");
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
