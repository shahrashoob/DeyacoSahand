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
        Schema::table('machine_allocation_packing_form', function (Blueprint $table) {
            //
            $table->foreignId("order_id")->after("machine_id")->nullable()->index()->
            comment("در صورتی که تخصیص از نوع تامین کالای امانی باشد،ستون  سفارش پر می شود.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation_packing_form', function (Blueprint $table) {
            //
        });
    }
};
