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
        Schema::table('warehouse_shelving', function (Blueprint $table) {
            //
            $table->integer("updown_code")->default(1)->comment("1:up,2:down");
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
