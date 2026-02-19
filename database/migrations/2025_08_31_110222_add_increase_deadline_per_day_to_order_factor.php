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
            $table->double('increase_deadline_per_day',8,6)->default(0)->after('cash_off_price')->nullable()->comment("مقدار افزایش قیمت با توجه به راس چک ها");
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
