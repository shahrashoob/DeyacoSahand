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
        Schema::create('unit_of_measure_types', function (Blueprint $table) {
            $table->id();
            $table->string('caption');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `unit_of_measure_types` comment 'نوع نمایش واحد کالا در تولید و فروش'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_of_measure_types');
    }
};
