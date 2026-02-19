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
        Schema::table('machine_type_input_band_goods_kind', function (Blueprint $table) {
            //
            $table->index('machine_type_input_band_id','mtibgk_machine_type');
            $table->index('goods_kind_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_type_input_band_goods_kind', function (Blueprint $table) {
            //
        });
    }
};
