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
        Schema::table('order_packing_form', function (Blueprint $table) {
            //
            $table->foreignId("packing_form_id")->after("order_id")->nullable()->
                comment("شناسه فرم بسته بندی که در سامانه ایجاد شده است.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_packing_form', function (Blueprint $table) {
            //
        });
    }
};
