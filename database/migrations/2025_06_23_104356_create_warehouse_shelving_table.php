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
        Schema::create('warehouse_shelving', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_id");
            $table->string("caption");
            $table->string("code");
            $table->integer("parent_id")->nullable();
            $table->string("fullCode");
            $table->string("fullCaption");
            $table->integer("warehouse_shelving_line_type_id")->comment("روش نام گذاری ردیف (1: عددی، 2: حروف بزرگ انگلیسی و ...)");
            $table->integer("warehouse_shelving_line_part")->comment("تعداد بخش های ردیف (001 و 021 و 123)");
            $table->integer("warehouse_shelving_line_status_id")->comment("وضعیت نمایش کد در کد سلول");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_shelving');
    }
};
