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
            //
            $table->string("pin2")->nullable()->after("pin1")->comment("پین 2");
            $table->string("tag2")->nullable()->after("tag1")->comment("تگ 2");
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
