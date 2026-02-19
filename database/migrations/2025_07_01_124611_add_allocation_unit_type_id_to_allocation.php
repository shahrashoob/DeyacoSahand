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
        Schema::table('allocations', function (Blueprint $table) {
            //
            $table->foreignId("allocation_unit_type_id")->default(1)->after("status_id")->comment("نوع تخصیص بر اساس کدام واحد کالا بوده است.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allocation', function (Blueprint $table) {
            //
        });
    }
};
