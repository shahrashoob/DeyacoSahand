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
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
            $table->integer('max_number_of_packing_forms')->after('min_number_of_packing_forms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
        });
    }
};
