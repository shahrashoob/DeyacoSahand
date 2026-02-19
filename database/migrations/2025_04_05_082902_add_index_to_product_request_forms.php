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
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
            $table->index('status_id');
            $table->index('form_id');
            $table->index('allocation_id');
            $table->index('user_id');
            $table->index('warehouse_id');
            $table->index('order_id');
            $table->index('active_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
        });
    }
};
