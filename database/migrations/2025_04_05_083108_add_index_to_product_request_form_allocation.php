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
        Schema::table('product_request_form_allocation', function (Blueprint $table) {
            //
            $table->index('product_request_form_id');
            $table->index('script_log_id');
            $table->index('allocation_id');
            $table->index('material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_form_allocation', function (Blueprint $table) {
            //
        });
    }
};
