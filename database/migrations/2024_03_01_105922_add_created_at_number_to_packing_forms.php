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
        Schema::table('packing_forms', function (Blueprint $table) {
            //
            $table->integer("created_at_number")->nullable()->index()->comment("تاریخ ایجاد به صورت عددی 14021207");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
        });
    }
};
