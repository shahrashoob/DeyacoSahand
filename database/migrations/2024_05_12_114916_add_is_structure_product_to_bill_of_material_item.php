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
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
            $table->foreignId("is_structure_product")->default(0)->comment("آیا این ماده اولیه، کالای ساختاری می باشد؟");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_of_materail_item', function (Blueprint $table) {
            //
        });
    }
};
