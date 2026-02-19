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
        Schema::table('car_types', function (Blueprint $table) {
            //
            $table->float('min_weight')->after("caption");
            $table->float('max_weight')->after("caption");
            $table->float('min_volume')->after("caption");
            $table->float('max_volume')->after("caption");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_types', function (Blueprint $table) {
            //
        });
    }
};
