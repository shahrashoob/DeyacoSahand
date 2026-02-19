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
        Schema::table('button_post', function (Blueprint $table) {
            //
            $table->index('post_id');
            $table->index('button_id');
            $table->index('permission_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('button_post', function (Blueprint $table) {
            //
        });
    }
};
