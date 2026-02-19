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
        Schema::table('form_general_item', function (Blueprint $table) {
            //
            $table->double('kg_in_meter')->nullable()->comment("گرماژ کالا برای بسته بندی های داخل فرم");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_general_item', function (Blueprint $table) {
            //
        });
    }
};
