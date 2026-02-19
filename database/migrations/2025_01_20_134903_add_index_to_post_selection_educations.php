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
        Schema::table('post_selection_educations', function (Blueprint $table) {
            //
            $table->index('education_id');
            $table->index('selection_id');
            $table->index('post_id');
            $table->index('post_selection_setting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_selection_educations', function (Blueprint $table) {
            //
        });
    }
};
