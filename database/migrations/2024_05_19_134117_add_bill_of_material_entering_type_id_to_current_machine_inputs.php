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
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
            $table->foreignId("bill_of_material_entering_type_id")->index()->default(1)->
            comment("روش ورود کالا به ماشین");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
        });
    }
};
