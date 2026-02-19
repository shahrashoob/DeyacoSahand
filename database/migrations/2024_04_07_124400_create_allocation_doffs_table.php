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
        Schema::create('allocation_doffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("allocation_id");
            $table->foreignId("packing_type_id");
            $table->integer("max_number_of_doffs")->comment("حداکثر تعداد داف در هر تخصیص ");
            $table->integer("amount_of_each_doffs")->comment("مقدار هر داف  ");
            $table->integer("number_of_doffs_done")->default(0)->comment("تعداد داف های انجام شده  ");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `allocation_doffs` comment 'ذخیره اطلاعات داف به ازای هر نوع بسته بندی'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_doffs');
    }
};
