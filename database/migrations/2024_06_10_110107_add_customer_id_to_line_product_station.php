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
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->foreignId("customer_id")->nullable()->
            comment("نام مشتری در کالاهای تحویل امانی")->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
        });
    }
};
