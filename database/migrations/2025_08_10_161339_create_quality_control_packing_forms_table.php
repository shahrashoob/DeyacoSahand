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
        Schema::create('quality_control_packing_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_form_id');
            $table->foreignId('packing_form_item_id');
            $table->foreignId('production_id');
            $table->foreignId('production_form_id');
            $table->foreignId('production_form_item_id');
            $table->foreignId('product_id');
            $table->foreignId('degree_id');
            $table->foreignId('lot_number_id');
            $table->foreignId('band_code');
            $table->float('start_point');
            $table->float('end_point');
            $table->double('amount',15,6);
            $table->double('amount_after_control',15,6);
            $table->double('final_amount',15,6);
            $table->double('sub_amount',15,6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_control_packing_forms');
    }
};
