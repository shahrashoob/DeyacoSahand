<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->comment('پست');
            $table->foreignId("user_id")->comment('کاربر');
            $table->foreignId("status_id");
            $table->dateTime("last_completion_date_time")->comment('اخرین زمان مهلت تکمیل فرم ارزیابی');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `evaluation_forms` comment 'فرم ارزیابی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_forms');
    }
}
