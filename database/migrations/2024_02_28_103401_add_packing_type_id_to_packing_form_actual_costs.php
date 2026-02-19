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
        Schema::table('packing_form_actual_costs', function (Blueprint $table) {
            //
            $table->foreignId("packing_type_id")->after("packing_form_id")->index();
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
