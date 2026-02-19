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
            $table->longText('reservoirs_rows')->nullable()->comment("اطلاعات مخزن جهت ثبت اطلاعات");
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
