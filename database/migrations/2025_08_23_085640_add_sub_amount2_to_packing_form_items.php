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
        Schema::table('packing_form_item', function (Blueprint $table) {
            //
            $table->double('sub_amount2', 10, 4)->nullable()->after('sub_amount')->comment("مقدار واحد فرعی 2");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_form_items', function (Blueprint $table) {
            //
        });
    }
};
