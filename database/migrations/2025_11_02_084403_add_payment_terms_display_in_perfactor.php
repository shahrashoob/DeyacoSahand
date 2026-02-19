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
        Schema::table('customers', function (Blueprint $table) {
            //
            $table->integer('payment_terms_display_in_per_factor')->default(0)->comment("آیا شرایط پرداخت در پیش فاکتور نمایش داده شود؟");
            $table->string('payment_terms')->comment("شرایط پرداخت");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perfactor', function (Blueprint $table) {
            //
        });
    }
};
