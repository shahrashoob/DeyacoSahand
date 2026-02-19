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
        Schema::table('order_packing_form', function (Blueprint $table) {
            //
            $table->foreignId("order_list_id")->after("order_id")->index()->comment("هر بسته بندی به ازای هر ردیف سفارش ثبت می شود");
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
