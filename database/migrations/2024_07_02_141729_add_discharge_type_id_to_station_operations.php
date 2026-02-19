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
        Schema::table('station_operations', function (Blueprint $table) {
            //
            $table->foreignId("discharge_type_id")->index()->default(2)->comment("نوع حرکت مواد اولیه در ماشین FiFo/LiFo");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('station_operations', function (Blueprint $table) {
            //
        });
    }
};
