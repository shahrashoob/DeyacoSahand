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
        Schema::table('product_request_form_packing_type', function (Blueprint $table) {
            //
            $table->index('product_request_form_id',"PRFPT_product_request_form_id");
            $table->index('product_request_form_item_id',"PRFPT_product_request_form_item_id");
            $table->index('product_id');
            $table->index('packing_type_id');
            $table->index('degree_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_form_packing_type', function (Blueprint $table) {
            //
        });
    }
};
