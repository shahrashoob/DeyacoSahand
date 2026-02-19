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
        Schema::table('production_form_item', function (Blueprint $table) {
            //
            $table->foreignId('machine_allocation_id')->nullable()->after('allocation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_form_item', function (Blueprint $table) {
            //
        });
    }
};
