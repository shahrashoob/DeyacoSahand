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
            $table->bigInteger("created_day_number")->index()->comment("عدد زمان (روز)");
            $table->renameColumn("created_time_number","created_minute_number");
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
