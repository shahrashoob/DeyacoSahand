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
        Schema::table('product_tariff_logs', function (Blueprint $table) {
            //
            $table->float('increase_percentage_deadline_per_day', 8, 2)->default(0)->after('consumer_price')->
            comment("قیمت پیش فاکتور با توجه به راس پرداخت یک درصدی به ازای هر روز اضافه شود");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_tariff_logs', function (Blueprint $table) {
            //
        });
    }
};
