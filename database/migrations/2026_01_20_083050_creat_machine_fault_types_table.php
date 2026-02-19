<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_fault_types', function (Blueprint $table) {
            $table->id();
            $table->text('caption');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_fault_types');
    }
};
