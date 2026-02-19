<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePostInterviewEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_interview_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("education_id")->comment('اموزش');
            $table->foreignId("interview_id")->comment('مصاحبه');
            $table->foreignId("post_id")->comment('پست');
            $table->foreignId("post_interview_setting_id")->comment('تنظیمات مصاحبه');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `post_interview_educations` comment 'جدول تعریف پیش نیاز'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_interview_educations');
    }
}
