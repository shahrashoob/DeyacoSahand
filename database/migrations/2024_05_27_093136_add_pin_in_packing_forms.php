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
        Schema::table('packing_forms', function (Blueprint $table) {
            $table->string('pin')->nullable()->comment("پین");
            $table->string('tag')->nullable()->comment("تگ");
            $table->foreignId('source_packaging_form_id')->nullable()->comment("کد بسته بندی مبدا");
            $table->foreignId('destination_packing_form_id')->nullable()->comment("کد بسته بندی مقصد");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
        });
    }
};
