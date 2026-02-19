<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarpsRequestFormLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warps_request_form_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warps_request_form_id");

            $table->foreignId("status_id");
            $table->foreignId("message_id")->nullable();
            $table->foreignId("user_id");

            $table->datetime("created_at")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime("updated_at")->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warps_request_form_logs');
    }
}
