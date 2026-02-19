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
        Schema::table('post_evaluation_indicators', function (Blueprint $table) {
            //
            $table->index('post_id');
            $table->index('evaluation_type_id');
            $table->index('evaluation_indicator_id');
            $table->index('post_evaluation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_evaluation_indicators', function (Blueprint $table) {
            //
        });
    }
};
