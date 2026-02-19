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
        Schema::table('reservoirs', function (Blueprint $table) {
            //
            $table->foreignId("packing_form_id")->nullable()->comment("کد بسته بندی مرتبط با مخزن( یکتا و یکبار تولید می شود)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
