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
        Schema::table('goods_kind_packing_type', function (Blueprint $table) {
            //
            $table->index('packing_type_id');
            $table->index('goods_kind_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kind_packing_type', function (Blueprint $table) {
            //
        });
    }
};
