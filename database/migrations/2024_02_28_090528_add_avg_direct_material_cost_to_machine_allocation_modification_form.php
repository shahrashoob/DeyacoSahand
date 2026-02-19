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
        Schema::table('machine_allocation_modification_form', function (Blueprint $table) {
            //
            $table->double("avg_material_cost",15,4)->nullable()->comment("میانگین بهای تمام شده کالا در برگشت مواد اولیه (یک واحد کالا)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation_modification_form', function (Blueprint $table) {
            //
        });
    }
};
