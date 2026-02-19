<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeAutomationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("office_automation_work_id");
            $table->foreignId("office_automation_to_do_list_id");
            $table->foreignId("user_id");
            $table->foreignId("status_id");
            $table->foreignId("event_id");
            $table->foreignId("message_id")->nullable();
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
        Schema::dropIfExists('office_automation_logs');
    }
}
