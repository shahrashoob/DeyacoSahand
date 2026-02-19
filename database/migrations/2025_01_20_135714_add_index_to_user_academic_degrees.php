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
        Schema::table('user_academic_degrees', function (Blueprint $table) {
            //
            $table->index('user_id');
            $table->index('academic_degree_id');
            $table->index('academic_degree_type_id');
            $table->index('academic_degree_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_academic_degrees', function (Blueprint $table) {
            //
        });
    }
};
