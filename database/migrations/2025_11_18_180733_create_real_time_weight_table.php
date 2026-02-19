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
        Schema::create('real_time_orders', function (Blueprint $table) {
            $table->id();
            $table->date('current_date')->comment("تاریخ جاری");
            $table->integer('date_number')->comment("کپشن روز");
            $table->double('weight')->comment("وزن بار ارسال شده به تن");
            $table->double("total_price")->comment("جمع کل فروش");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_time_weight');
    }
};
