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
        Schema::create('product_request_form_packing_type_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_request_form_id');
            $table->foreignId('product_request_form_item_id');
            $table->foreignId('product_id');
            $table->foreignId('packing_type_id');
            $table->foreignId('degree_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_request_form_packing_type_log');
    }
};
