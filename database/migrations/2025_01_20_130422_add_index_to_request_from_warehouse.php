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
        Schema::table('request_from_warehouse', function (Blueprint $table) {
            //
            $table->index("allocation_id");
            $table->index("order_id");
            $table->index("order_list_id");
            $table->index("product_id");
            $table->index("customer_id");
            $table->index("production_card_id");
            $table->index("perchase_order_id");
            $table->index("call_id");
            $table->index("material_id");
            $table->index("alternative_material_id");
//            $table->index("replace_product_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse', function (Blueprint $table) {
            //
        });
    }
};
