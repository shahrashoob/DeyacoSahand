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
        Schema::create('form_general_item_packing_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId("packing_form_id");
            $table->foreignId("form_id");
            $table->foreignId("form_general_item_id");
            $table->foreignId("check_quality_status_id");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_general_item_packing_forms');
    }
};
