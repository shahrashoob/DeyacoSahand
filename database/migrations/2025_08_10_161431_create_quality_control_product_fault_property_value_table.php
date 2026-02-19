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

            Schema::create('quality_control_fault_property_value', function (Blueprint $table) {

                $table->id();
                $table->foreignId('packing_form_id');
                $table->foreignId('packing_form_item_id');
                $table->foreignId('production_id');
                $table->foreignId('production_form_id');
                $table->foreignId('production_form_item_id');
                $table->foreignId('product_id');
                $table->foreignId('product_fault_id');
                $table->foreignId('product_fault_property_id');
                $table->string('value')->nullable();
                $table->timestamps();

            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_control_fault_property_value');
    }
};
