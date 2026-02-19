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
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
            $table->foreignId('product_request_form_type_id')->default(1)->comment("نوع ثبت درخواست کالا به انبار");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
        });
    }
};
