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
        Schema::table('production_cards', function (Blueprint $table) {
            //
            $table->integer("number_of_packing_form")->nullable()->
            after("number")->
            comment("تعداد بسته بندی های پایین ترین سطح کارت تولید");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_cards', function (Blueprint $table) {
            //
        });
    }
};
