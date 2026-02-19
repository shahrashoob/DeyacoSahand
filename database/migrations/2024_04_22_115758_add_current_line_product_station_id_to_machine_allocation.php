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
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
            $table->foreignId("line_product_station_id")->index()->nullable()->
            comment("شناسه ردیف مسیرمحصول کالا که در حال انجام عملیات می باشد یا در انتظار تخصیص مجدد برای این خط محصول می باشد..");
        });
        Schema::table('allocations', function (Blueprint $table) {
            //
            $table->dropColumn("current_line_product_station_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocaiton', function (Blueprint $table) {
            //
        });
    }
};
