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
        Schema::table('goods_kind_property_product_type', function (Blueprint $table) {
            //
            $table->index('goods_kind_id');
            $table->index('goods_kind_property_id');
            $table->index('product_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kind_property_product_type', function (Blueprint $table) {
            //
        });
    }
};
