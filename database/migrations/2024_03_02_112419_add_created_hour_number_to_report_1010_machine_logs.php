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
        Schema::table('report_1010_machine_logs', function (Blueprint $table) {
            //
            $table->bigInteger("created_hour_number")->index()->nullable()->comment("مقدار ساعت");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_1010_machine_logs', function (Blueprint $table) {
            //
        });
    }
};
