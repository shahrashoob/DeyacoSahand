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
        Schema::table('product_request_form_delivery', function (Blueprint $table) {
            //
            $table->index('product_request_form_id');
            $table->index('packing_form_id');
            $table->index('packing_type_id');
            $table->index('carrier_id');
            $table->index('unit_id');
            $table->index('transport_item_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_form_delivery', function (Blueprint $table) {
            //
        });
    }
};
