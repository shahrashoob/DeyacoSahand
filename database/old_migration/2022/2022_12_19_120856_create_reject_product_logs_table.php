<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectProductLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reject_product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("reject_product_form_id");
            $table->foreignId("event_id");
            $table->foreignId("status_id");
            $table->foreignId("message_id")->nullable();
            $table->foreignId("user_id");
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
        Schema::dropIfExists('reject_product_logs');
    }
}
