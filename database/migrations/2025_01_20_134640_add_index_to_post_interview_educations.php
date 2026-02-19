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
        Schema::table('post_interview_educations', function (Blueprint $table) {
            //
            $table->index('education_id');
            $table->index('interview_id');
            $table->index('post_id');
            $table->index('post_interview_setting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_interview_educations', function (Blueprint $table) {
            //
        });
    }
};
