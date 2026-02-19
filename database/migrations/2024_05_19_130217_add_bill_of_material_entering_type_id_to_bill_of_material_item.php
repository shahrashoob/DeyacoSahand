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
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
            $table->foreignId("bill_of_material_entering_type_id")->default(1)->index()->
            comment("نوع ورود مواد اولیه به ماشین: پیوسته یا ناپیوسته با بسته بندی و ...");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
        });
    }
};
