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
        Schema::table('import_leave_reminders', function (Blueprint $table) {
            //
            $table->string('start_date_jalali')->nullable();
            $table->string('end_date_jalali')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_leave_reminders', function (Blueprint $table) {
            //
        });
    }
};
