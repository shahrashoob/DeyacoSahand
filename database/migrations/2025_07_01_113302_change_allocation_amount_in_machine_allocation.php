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
            $table->double('allocation_amount')->nullable()->change();
            $table->double('amount_of_each_doffs')->nullable()->change();
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
