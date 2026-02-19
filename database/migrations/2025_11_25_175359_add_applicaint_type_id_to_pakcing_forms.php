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
        Schema::table('packing_forms', function (Blueprint $table) {
            //
            $table->foreignId('applicant_type_id')->nullable();
            $table->foreignId('applicant_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pakcing_forms', function (Blueprint $table) {
            //
        });
    }
};
