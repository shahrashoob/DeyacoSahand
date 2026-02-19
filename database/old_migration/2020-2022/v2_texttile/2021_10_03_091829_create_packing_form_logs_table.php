<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingFormLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_form_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("packing_form_id");
            $table->foreignId("packing_form_item_id")->nullable();
            $table->foreignId("status_id");
            $table->foreignId("event_id");
            $table->foreignId("message_id")->nullable();
            $table->integer("user_id")->default(0);
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
        Schema::dropIfExists('packing_form_logs');
    }
}
