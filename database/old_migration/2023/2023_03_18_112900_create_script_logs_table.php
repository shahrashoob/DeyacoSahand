<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScriptLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('script_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("script_id");
            $table->foreignId("other_id");
            $table->foreignId("contour");
            $table->foreignId("run_status_id");
            $table->foreignId("event_id");
            $table->foreignId("user_id");
            $table->longText("data")->nullable();
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
        Schema::dropIfExists('script_logs');
    }
}
