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
        Schema::table('machine_allocation_actual_consumption', function (Blueprint $table) {
            //
            $table->double("cost_of_one_unit", 15, 4)->after("calculated_amount_till_now")->nullable()->comment("هزینه ماده اولیه در تخصیص به ازای تولید یک واحد کالا");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation_actual_consumption', function (Blueprint $table) {
            //
        });
    }
};
