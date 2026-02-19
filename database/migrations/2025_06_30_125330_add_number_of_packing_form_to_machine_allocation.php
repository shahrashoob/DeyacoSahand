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
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
            $table->integer('number_of_packing_form')->nullable()->
            after('allocation_amount')->comment("تعداد بسته بندی در تخصیص");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
        });
    }
};
