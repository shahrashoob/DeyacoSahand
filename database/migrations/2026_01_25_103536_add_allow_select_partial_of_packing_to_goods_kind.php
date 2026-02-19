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
            $table->foreignId("allow_select_partial_of_packing_in_output")->default(0)->comment("آیا امکان انتخاب از کالا در داشبورد خروج از کالا امکان پذیر است؟");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kind', function (Blueprint $table) {
            //
        });
    }
};
