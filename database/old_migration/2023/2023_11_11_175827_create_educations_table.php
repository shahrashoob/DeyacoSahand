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
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("education_type_id")->comment('نوع اموزش');
            $table->foreignId("educational_text_file_id")->nullable()->comment('فایل متنی ');
            $table->foreignId("educational_video_file_id")->nullable()->comment('فایل ویدویی');
            $table->string("caption")->comment('عنوان اموزش');
            $table->string("exam_type_id")->comment('عنوان اموزش');
            $table->integer("minimum_score_to_confirm_the_education")->comment('حداقل امتیاز برای تایید اموزش');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `educations` comment 'جدول برای افزودن آموزش'");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};
