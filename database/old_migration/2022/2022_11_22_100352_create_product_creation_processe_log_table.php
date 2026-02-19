<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCreationProcesseLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_creation_process_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("product_creation_process_id");
            $table->foreignId("event_id");
            $table->foreignId("status_id");
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
        Schema::dropIfExists('product_creation_processe_log');
    }
}
