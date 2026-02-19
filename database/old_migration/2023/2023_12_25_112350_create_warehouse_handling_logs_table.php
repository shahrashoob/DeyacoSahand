<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseHandlingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_handling_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_handling_id");
            $table->foreignId("status_id");
            $table->foreignId("event_id");
            $table->foreignId("user_id");
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
        Schema::dropIfExists('warehouse_handling_logs');
    }
}
