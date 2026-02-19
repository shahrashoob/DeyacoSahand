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
        Schema::table('machine_types', function (Blueprint $table) {
            //
            $table->foreignId("number_of_contour_in_minute")->nullable()->comment("کارکرد کنتور اصلی در دقیقه");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
        });
    }
};
