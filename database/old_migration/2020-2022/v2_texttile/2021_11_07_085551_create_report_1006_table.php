<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReport1006Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_1006_production_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId("owner_user_id");
            $table->foreignId("production_form_id");
            $table->foreignId("event_id");
            $table->dateTime("log_created_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_1006');
    }
}
