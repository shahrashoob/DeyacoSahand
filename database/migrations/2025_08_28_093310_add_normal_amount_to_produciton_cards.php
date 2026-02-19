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
            $table->float('normal_amount', 8, 2)->nullable()->after("number")->comment("مقدار نرمال بسته بندی ها ");
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
