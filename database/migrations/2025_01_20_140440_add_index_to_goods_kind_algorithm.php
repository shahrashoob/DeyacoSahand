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
        Schema::table('goods_kind_algorithm', function (Blueprint $table) {
            //
            $table->index('goods_kind_id');
            $table->index('supply_type_id');
            $table->index('production_card_allocation_algorithm_id',"production_card_allocation_algorithm_id");
            $table->index('production_card_create_algorithm_id',"production_card_create_algorithm_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kind_algorithm', function (Blueprint $table) {
            //
        });
    }
};
