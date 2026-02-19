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
        Schema::create('packing_form_actual_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("packing_form_id")->index();
            $table->foreignId("product_id")->index();
            $table->foreignId("actual_cost_type_id")->index();
            $table->double("cost_of_one_unit",15,4)->nullable()->comment("هزینه یک واحد کالا");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_form_actual_costs');
    }
};
