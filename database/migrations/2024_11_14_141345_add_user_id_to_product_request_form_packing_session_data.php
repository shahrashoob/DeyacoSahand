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
        Schema::table('product_request_form_packing_session_data', function (Blueprint $table) {
            //
            $table->foreignId('user_id')->index()->nullable();
            $table->foreignId('product_request_form_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_form_packing_session_data', function (Blueprint $table) {
            //
        });
    }
};
