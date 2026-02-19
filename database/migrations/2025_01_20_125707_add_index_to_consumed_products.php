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
        Schema::table('consumed_products', function (Blueprint $table) {
            //
            $table->index("product_id");
            $table->index("material_id");
            $table->index("product_creation_process_id");
            $table->index("status_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumed_products', function (Blueprint $table) {
            //
        });
    }
};
