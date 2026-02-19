<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('new_tariff_product', function (Blueprint $table) {
            //
            $table->string("service_code")->nullable()->comment("کد خدمت برای کالاهای فروش کارمزید");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_tariff_product', function (Blueprint $table) {
            //
        });
    }
};
