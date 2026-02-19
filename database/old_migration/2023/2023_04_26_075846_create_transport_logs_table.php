<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_logs', function (Blueprint $table) {
            $table->id();
            $table->integer("transport_id");
            $table->integer("status_id");
            $table->integer("event_id");
            $table->integer("message_id")->nullable();
            $table->integer("user_id")->default(0);

            $table->datetime("created_at")->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_logs');
    }
}
