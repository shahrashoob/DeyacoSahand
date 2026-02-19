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
        Schema::table('machine_allocation', function (Blueprint $table) {
            $table->foreignId("customer_id")->nullable()->index()->after("supplier_id")->
            comment("کالاهای دریافت امانی به مشتری تخصیص داده می شود.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocations', function (Blueprint $table) {
            //
        });
    }
};
