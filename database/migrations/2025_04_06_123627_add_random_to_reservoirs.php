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
        Schema::table('reservoirs', function (Blueprint $table) {
            //
            $table->string("random")->comment("کد رندوم جهت مخزن");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservoirs', function (Blueprint $table) {
            //
        });
    }
};
