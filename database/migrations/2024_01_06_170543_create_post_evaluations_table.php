<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePostEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->comment('پست');
            $table->foreignId("evaluation_type_id")->comment('نوع ارزیابی');
            $table->string("cron")->comment('دوره ارزیابی');
            $table->integer("time_of_complete")->comment('(ساعت)مدت زمان تکمیل فرم ارزیابی');
            $table->integer("active_status_id")->default(1200)->comment('وضعیت فعال بودن-1100');
            $table->dateTime("last_run_date_time")->comment('اخرین زمان ایجاد فرم ارزیابی');
            $table->dateTime("next_must_run_date_time")->comment('زمان بعدی ایجاد فرم ارزیابی');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `post_evaluations` comment 'پست- ارزیاب'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_evaluations');
    }
}
