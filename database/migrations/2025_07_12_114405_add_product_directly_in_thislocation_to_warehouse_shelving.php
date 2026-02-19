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
        Schema::table('warehouse_shelving', function (Blueprint $table) {
            //
            $table->integer('can_product_directly_in_location')->default(1)->after('updown_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_shelving', function (Blueprint $table) {
            //
        });
    }
};
