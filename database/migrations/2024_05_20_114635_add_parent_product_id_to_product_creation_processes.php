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
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
            $table->foreignId("parent_product_id")->index()->
            nullable()->
            comment("شناسه کالایی که از روی آن تعریف سریع کالا انجام شده است.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
        });
    }
};
