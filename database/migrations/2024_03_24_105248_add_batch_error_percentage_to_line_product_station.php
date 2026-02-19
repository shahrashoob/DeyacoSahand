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
            $table->integer("batch_error_percentage")->nullable()->after("batch")->comment("درصد خطای بچ");
            $table->foreignId("material_id_dependent_to_batch")->nullable()->after("batch")->comment("مواد اولیه وابسته به بچ");
            $table->foreignId("material_unit_type_id_dependent_to_batch")->nullable()->after("batch")->comment("واحد مواد اولیه وابسته به بچ");
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
