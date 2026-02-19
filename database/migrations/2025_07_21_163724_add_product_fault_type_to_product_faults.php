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
        Schema::table('product_faults', function (Blueprint $table) {
            //
            $table->integer("product_fault_type_id")->nullable()->comment("نوع نقص: 1-نقطه ای 2-پیوسته" );
            $table->integer("product_fault_fixed_type_id")->nullable()->comment("آیا نقص رفع شدنی است؟ 1-بله 2-خیر 3-مشخص نیست" );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_faults', function (Blueprint $table) {
            //
        });
    }
};
