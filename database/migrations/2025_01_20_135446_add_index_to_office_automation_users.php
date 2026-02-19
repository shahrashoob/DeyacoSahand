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
        Schema::table('office_automation_users', function (Blueprint $table) {
            //
            $table->index('user_id');
            $table->index('office_automation_work_id');
            $table->index('status_id');
            $table->index('priority_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_automation_users', function (Blueprint $table) {
            //
        });
    }
};
