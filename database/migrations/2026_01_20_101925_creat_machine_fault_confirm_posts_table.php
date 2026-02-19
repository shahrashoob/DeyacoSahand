<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_fault_confirm_posts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('machine_id');
            $table->unsignedBigInteger('machine_fault_type_ids');

            $table->timestamps();

            // اگر foreign key داری (پیشنهادی)
            // $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
            // $table->foreign('machine_id')->references('id')->on('machines')->cascadeOnDelete();
            // $table->foreign('machine_fault_type_id')->references('id')->on('machine_fault_types')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_fault_confirm_posts');
    }
};
