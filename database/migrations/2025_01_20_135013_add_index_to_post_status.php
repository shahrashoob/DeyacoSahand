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
        Schema::table('post_status', function (Blueprint $table) {
            //
            $table->index('post_id');
            $table->index('status_id');
            $table->index('permission_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_status', function (Blueprint $table) {
            //
        });
    }
};
