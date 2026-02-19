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
        Schema::table('machine_allocation_modification_packing_form', function (Blueprint $table) {
            //
            $table->double("consumed_amount", 15, 7)->nullable()->comment("مقداری از بسته بندی که در برگشت مصرف شده است.");
            $table->double("material_cost",15,4)->nullable()->comment("بهای تمام شده بسته بندی (به ازای یک واحد کالا)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation_modification_packing_form', function (Blueprint $table) {
            //
        });
    }
};
