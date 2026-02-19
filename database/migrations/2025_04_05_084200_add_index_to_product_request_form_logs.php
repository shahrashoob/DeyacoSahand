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
        Schema::table('product_request_form_logs', function (Blueprint $table) {
            //
            $table->index('product_request_form_id');
            $table->index('status_id');
            $table->index('message_id');
            $table->index('user_id');
            $table->index('form_id');
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_form_logs', function (Blueprint $table) {
            //
        });
    }
};
