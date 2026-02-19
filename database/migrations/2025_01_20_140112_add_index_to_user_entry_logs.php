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
        Schema::table('user_entry_logs', function (Blueprint $table) {
            //
            $table->index('user_id');
            $table->index('user_status_id');
            $table->index('entry_register_user_id');
            $table->index('entry_permit_status_id');
            $table->index('exit_register_user_id');
            $table->index('exit_permit_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_entry_logs', function (Blueprint $table) {
            //
        });
    }
};
