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
        Schema::table('machines', function (Blueprint $table) {
            //
            $table->foreignId('next_relation_machine_code')->nullable()->comment("کد ماشین متناظر در ایستگاه بعدی");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahines', function (Blueprint $table) {
            //
        });
    }
};
