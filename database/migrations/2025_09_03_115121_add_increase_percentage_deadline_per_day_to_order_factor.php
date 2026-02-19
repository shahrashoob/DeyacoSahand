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
        Schema::table('order_factor', function (Blueprint $table) {
            //
            $table->decimal('increase_percentage_deadline_per_day', 15, 6)->after('increase_deadline_per_day')->comment("درصد افزایش قیمت به ازای هر روز ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_factor', function (Blueprint $table) {
            //
        });
    }
};
