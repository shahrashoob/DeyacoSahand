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
        Schema::table('carrier_type_goods_kind', function (Blueprint $table) {
            //
            $table->index(['carrier_type_id', 'goods_kind_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrier_type_goods_kind', function (Blueprint $table) {
            //
        });
    }
};
