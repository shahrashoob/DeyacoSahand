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
            $table->foreignId("service_id")->after("product_id")->comment("شناسه خدمت برای کالاهای فروش کارمزدی")->nullable();

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
