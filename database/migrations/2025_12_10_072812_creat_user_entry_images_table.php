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
        Schema::create('entry_user_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // رابطه با users
            $table->string('name');   // نام عکس
            $table->string('path');   // مسیر ذخیره
            $table->boolean('trained')->default(false); // وضعیت آموزش
            $table->timestamp('trained_at')->nullable(); // زمان آموزش
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_users_images');
    }
};

