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
        Schema::create('product_fault_properties', function (Blueprint $table) {

            $table->id();
            $table->string("caption")->comment("نام مشخصه");
            $table->foreignId("field_type_id")->comment("نوع فیلد");
            $table->foreignId("special_unit_id")->nullable();
            $table->integer("min_value")->nullable();
            $table->integer("max_value")->nullable();
            $table->integer("priority_number")->comment("اولویت نمایش")->nullable();
            $table->foreignId("status_id")->comment("فعال/غیرفعال")->default(1200);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_fault_properties');
    }
};
