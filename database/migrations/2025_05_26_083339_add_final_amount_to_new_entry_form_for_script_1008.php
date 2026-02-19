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
        Schema::table('new_entry_form_for_script_1008', function (Blueprint $table) {
            //
            $table->double('sub_amount', 17, 7)->after('packing_form_rows');
            $table->double('final_amount', 17, 7)->after('packing_form_rows');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_entry_form_for_script_1008', function (Blueprint $table) {
            //
        });
    }
};
