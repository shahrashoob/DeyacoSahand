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
        Schema::create("efficiency_based_on_machine_logs", function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_log_id")->index();
            $table->foreignId("machine_id")->index();
            $table->foreignId("product_id")->index();
            $table->foreignId("operator_id")->index();
            $table->foreignId("machine_allocation_id")->index();
            $table->integer("log_type_id")->comment("1: حقیقی، 2: مجازی، 3: iot");
            $table->double("theory_contour")->comment("مقدار تئوری کنتور");
            $table->double("operation_contour")->comment("مقدار عملیاتی کنتور");
            $table->integer("time_in_seconds")->comment("زمان به دقیقه");
            $table->float("amount")->comment("مقدار تولید");
            $table->double("contour_sum_value");
            $table->bigInteger("created_time_number")->index()->comment("عدد زمان (دقیقه)");
            $table->bigInteger("created_hour_number")->index()->comment("عدد زمان (ساعت)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('efficiency_based_on_machine_logs');
    }
};
