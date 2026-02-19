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
        Schema::table('packing_types', function (Blueprint $table) {
            //
            $table->integer('packaging_forms_include_brand')->default(0)->comment("آیا بسته بندی شامل عیب یابی (برند) می باشد؟");
            $table->integer('take_amount_from_parent_production_card')->default(0)->comment("آیا مقدار برند را از کارت سطح بالا می گیرید؟");
            $table->float('normal_amount')->nullable()->comment("مقدار نرمال بسته بندی");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_types', function (Blueprint $table) {
            //
        });
    }
};
