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
        Schema::table('packing_forms', function (Blueprint $table) {
            //
            $table->renameColumn("source_packaging_form_id","source_packaging_form_code");
            $table->renameColumn("destination_packing_form_id","destination_packing_form_code");
            $table->renameColumn("pin","pin1");
            $table->renameColumn("tag","tag1");


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
        });
    }
};
