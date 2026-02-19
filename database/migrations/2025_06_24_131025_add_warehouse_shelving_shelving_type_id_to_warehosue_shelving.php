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
        Schema::table('warehouse_shelving', function (Blueprint $table) {
            //
            $table->foreignId("warehouse_shelving_type_id")->comment("نوع طبقه بندی ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_shelving', function (Blueprint $table) {
            //
        });
    }
};
