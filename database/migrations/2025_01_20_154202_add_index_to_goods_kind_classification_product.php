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
        Schema::table('goods_kind_classification_product', function (Blueprint $table) {
            //
            $table->index('product_id');
            $table->index('goods_kind_classification_id','gkcp_goods_kind_classification_id');
            $table->index('goods_kind_classification_option_id','gkcp_goods_kind_classification_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kind_classification_product', function (Blueprint $table) {
            //
        });
    }
};
