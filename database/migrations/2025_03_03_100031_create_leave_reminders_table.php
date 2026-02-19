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
        Schema::create('leave_remainders', function (Blueprint $table) {
            $table->id();
            $table->integer("year")->default(0)->comment("سال مالی");
            $table->foreignId("user_id")->index();
            $table->foreignId("post_id")->nullable()->index();
            $table->foreignId("leave_type_id")->index();
            $table->date("start_date")->comment("تاریخ و ساعت شروع سال مالی");
            $table->date("end_date")->comment("تاریخ و ساعت پایان سال مالی");
            $table->integer("leave_in_start")->default(0)->comment("مرخصی ابتدای دوره (دقیقه)");
            $table->integer("leave_in_end")->default(0)->comment("مرخصی پایان دوره (دقیقه)");
            $table->integer("leave_remainder")->default(0)->comment("مقدار باقی مانده مرخصی (دقیقه)");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_reminders');
    }
};
