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
        Schema::table('products', function (Blueprint $table) {
            $table->integer("have_testing_before_production")->default(0)->comment("آیا کالا قبل از تولید تست دارد؟");
            $table->integer("testing_is_on_line_production")->default(0)->comment("آیا تست بر روی خط تولید انجام می شود؟");
            $table->float("testing_amount")->default(0)->comment("مقدار تست کالا");
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
