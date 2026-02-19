<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('education_types', function (Blueprint $table) {
            $table->id();
            $table->string('caption')->comment('عنوان');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `education_types` comment 'جدول انواع اموزش'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_types');
    }
};
