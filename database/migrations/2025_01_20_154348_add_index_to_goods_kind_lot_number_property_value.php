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
        Schema::table('goods_kind_lot_number_property_value', function (Blueprint $table) {
            //
            $table->index('product_id');
            $table->index('lot_number_id');
            $table->index('goods_kind_lot_number_property_id','gklnpv_goods_kind_lot_number_property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kind_lot_number_property_value', function (Blueprint $table) {
            //
        });
    }
};
