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
        Schema::table('user_operations', function (Blueprint $table) {
            //
            $table->integer("legal_operation_absence")->nullable()->after("legal_absence")->comment("مقدار غیبت در ساعت کار قانونی");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_operations', function (Blueprint $table) {
            //
        });
    }
};
