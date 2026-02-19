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

        Schema::create('quality_control_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_form_id');
            $table->foreignId('user_id');
            $table->integer('is_supervisor')->default(0);
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_control_users');
    }
};
