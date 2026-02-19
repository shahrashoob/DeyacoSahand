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
        Schema::table('goods_kind_properties', function (Blueprint $table) {
            //
            $table->index('goods_kind_id');
            $table->index('field_type_id');
            $table->index('parent_id');
            $table->index('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kind_properties', function (Blueprint $table) {
            //
        });
    }
};
