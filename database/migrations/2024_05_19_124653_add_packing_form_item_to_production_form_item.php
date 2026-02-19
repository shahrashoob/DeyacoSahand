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
        Schema::table('production_form_item', function (Blueprint $table) {
            //
            $table->
            foreignId("packing_form_item_id")->
            index()->
            nullable()->
            comment("کد آیتم بسته بندی که معادل آن آیتم فرم تولید ایجاد شده است. ");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_form_item', function (Blueprint $table) {
            //
        });
    }
};
