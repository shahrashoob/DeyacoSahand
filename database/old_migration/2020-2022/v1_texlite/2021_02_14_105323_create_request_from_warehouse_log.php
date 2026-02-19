<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestFromWarehouseLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_from_warehouse_log', function (Blueprint $table) {
            $table->id();
            $table->integer("rfw_id");
            $table->integer("user_id");
            $table->integer("status_id");
            $table->integer("message_id");
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
        Schema::dropIfExists('request_from_warehouse_log');
    }
}
