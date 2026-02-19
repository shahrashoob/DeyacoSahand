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
            $table->foreignId("status_id")->default(6070001)->index()->comment("وضعیت ارسال بسته بندی ها (6070)");
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
