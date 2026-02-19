<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationFormLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_form_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->comment('کاربر');
            $table->foreignId("evaluation_form_id")->comment('فرم ارزیابی');
            $table->foreignId("event_id")->comment("رویداد");
            $table->foreignId("status_id")->comment("وضعیت");
            $table->foreignId("message_id")->nullable();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `evaluation_form_logs` comment 'لاگ فرم ارزیابی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_form_logs');
    }
}
