<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packing_form_actual_costs', function (Blueprint $table) {
            //
            $table->foreignId("status_id")->after("actual_cost_type_id")->comment("6060")->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_form_actual_costs', function (Blueprint $table) {
            //
        });
    }
};
