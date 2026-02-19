<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostSelectionEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_selection_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("education_id")->comment('اموزش');
            $table->foreignId("selection_id")->comment('گزینش');
            $table->foreignId("post_id")->comment('پست');
            $table->foreignId("post_selection_setting_id")->comment('تنظیمات گزینش');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `post_selection_educations` comment 'جدول تعریف پیش نیاز'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_selection_educations');
    }
}
