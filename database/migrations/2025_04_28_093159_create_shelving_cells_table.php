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
        Schema::create('shelving_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->index();

            $table->integer('line1_number')->nullable()->comment("تعداد ردیف های 1");
            $table->string('line1_type')->nullable()->comment("روش نام گذاری ردیف (1: عددی، 2: حروف بزرگ انگلیسی و ...)");
            $table->integer('line1_part')->nullable()->comment("تعداد بخش های ردیف (001 و 021 و 123)");
            $table->integer('line1_status_id')->nullable()->comment("وضعیت نمایش ردیف در کد سلول");

            $table->integer('line2_number')->nullable()->comment("عداد ردیف های 2");
            $table->string('line2_type')->nullable()->comment("روش نام گذاری ردیف (1: عددی، 2: حروف بزرگ انگلیسی و ...)");
            $table->integer('line2_part')->nullable()->comment("تعداد بخش های ردیف (001 و 021 و 123)");
            $table->integer('line2_status_id')->nullable()->comment("وضعیت نمایش ردیف در کد سلول");

            $table->integer('line3_number')->nullable()->comment("عداد ردیف های 3");
            $table->string('line3_type')->nullable()->comment("روش نام گذاری ردیف (1: عددی، 2: حروف بزرگ انگلیسی و ...)");
            $table->integer('line3_part')->nullable()->comment("تعداد بخش های ردیف (001 و 021 و 123)");
            $table->integer('line3_status_id')->nullable()->comment("وضعیت نمایش ردیف در کد سلول");

            $table->integer('line4_number')->nullable()->comment("عداد ردیف های 4");
            $table->string('line4_type')->nullable()->comment("روش نام گذاری ردیف (1: عددی، 2: حروف بزرگ انگلیسی و ...)");
            $table->integer('line4_part')->nullable()->comment("تعداد بخش های ردیف (001 و 021 و 123)");
            $table->integer('line4_status_id')->nullable()->comment("وضعیت نمایش ردیف در کد سلول");

            $table->integer('line5_number')->nullable()->comment("عداد ردیف های 5");
            $table->string('line5_type')->nullable()->comment("روش نام گذاری ردیف (1: عددی، 2: حروف بزرگ انگلیسی و ...)");
            $table->integer('line5_part')->nullable()->comment("تعداد بخش های ردیف (001 و 021 و 123)");
            $table->integer('line5_status_id')->nullable()->comment("وضعیت نمایش ردیف در کد سلول");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelving_lines');
    }
};
