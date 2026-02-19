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
        Schema::table('warehouse_product', function (Blueprint $table) {
            //
            $table->index('warehouse_id');
            $table->index('product_id');
            $table->index('form_id');
            $table->index('form_item_id');
            $table->index('company_id');
            $table->index('packing_type_id');
            $table->index('carrier_id');
            $table->index('degree_id');
            $table->index('lot_number_id');
            $table->index('packing_form_item_id');
            $table->index('master_packing_form_id');
            $table->index('financial_software_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_product', function (Blueprint $table) {
            //
        });
    }
};
