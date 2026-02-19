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
        Schema::table('efficiency_based_on_machine_logs', function (Blueprint $table) {
            //

        });
        DB::statement("ALTER TABLE `efficiency_based_on_machine_logs` 	CHANGE COLUMN `amount` `amount` DOUBLE(15,6) NULL");
        DB::statement("ALTER TABLE `efficiency_based_on_machine_logs_m` 	CHANGE COLUMN `amount` `amount` DOUBLE(15,6) NULL");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('efficiency_based_on_machine_logs', function (Blueprint $table) {
            //
        });
    }
};
