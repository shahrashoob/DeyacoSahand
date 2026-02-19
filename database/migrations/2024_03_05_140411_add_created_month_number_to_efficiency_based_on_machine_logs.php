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
        Schema::table('efficiency_based_on_machine_logs', function (Blueprint $table) {
            //
            $table->integer("created_month_number")->nullable()->index()->comment("عدد زمان (ماه)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('efficiency_based_on_machine_logs', function (Blueprint $table) {
            //
        });
    }
};
