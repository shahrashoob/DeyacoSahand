<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialLicenseLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_license_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("special_license_id");
            $table->foreignId("status_id");
            $table->foreignId("event_id");
            $table->foreignId("user_id");
            $table->foreignId("post_id")->nullable();
            $table->foreignId("committee_id")->nullable();
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
        Schema::dropIfExists('special_license_logs');
    }
}
