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
            $table->foreignId('warehouse_storage_type_id')->nullable()->comment("نوع انبارش کالا");
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
