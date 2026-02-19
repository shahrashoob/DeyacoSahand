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
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
            $table->index("product_request_form_id");
            $table->index("production_id");
            $table->index("product_id");
            $table->index("warehouse_product_id");
            $table->index("order_list_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
        });
    }
};
