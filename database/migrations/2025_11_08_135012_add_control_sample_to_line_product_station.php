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
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->integer('has_control_sample')->comment("آیا مسیر محصول نیاز به نمونه شاهد دارد؟")->default(0);
            $table->integer('line_product_start_status_id')->default(3410001)->comment("نوع پیش نیازی در مسیر محصول دو حالت دارد (STS یا ETS / استارت به استارت یا پایان به استارت)")->default(0);
            $table->integer('delay_in_the_start_minute')->default(0)->comment("تاخیر در شروع عملیات های (STS) ( دقیقه)")->default(0);
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
