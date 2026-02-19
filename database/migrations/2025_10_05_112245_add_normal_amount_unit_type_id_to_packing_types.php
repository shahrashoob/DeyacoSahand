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
        Schema::table('packing_types', function (Blueprint $table) {
            //
            $table->foreignId('normal_amount_unit_type_id')->after('normal_amount')->default(1)->comment("نوع واحدی که مقدار نرمال را محاسبه می کنیم");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_types', function (Blueprint $table) {
            //
        });
    }
};
