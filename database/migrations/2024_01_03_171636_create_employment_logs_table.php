<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmploymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("employment_id")->comment("همکاری");
            $table->foreignId("status_id")->comment("وضعیت");
            $table->foreignId("event_id")->comment("رویداد");
            $table->foreignId("user_id")->comment("کاربر");
            $table->foreignId("message_id")->nullable()->comment("متن");
            $table->foreignId("employment_selection_id")->nullable()->comment("گزینش");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employment_logs');
    }
}
