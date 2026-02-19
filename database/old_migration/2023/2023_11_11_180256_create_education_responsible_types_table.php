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
        Schema::create('education_responsible_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId("education_id")->comment("اموزش");
            $table->foreignId("responsible_type_id")->comment("نوع مسول");
            $table->foreignId("post_id")->comment("پست");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `education_responsible_types` comment 'جدول برای افزودن مسولان اموزش'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_responsibles');
    }
};
