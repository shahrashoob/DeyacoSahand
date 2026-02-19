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
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
            $table->foreignId("replacement_status_id")->default(3359001)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
        });
    }
};
