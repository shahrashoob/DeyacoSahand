<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_status', function (Blueprint $table) {
            $table->id();
            $table->integer("old_status_id")->nullable();
            $table->integer("status_id");
            $table->string("table_name");
            $table->integer("message_id");
            $table->integer("status_type_id");
            $table->integer("user_id");
            $table->integer("other_id")->nullable();
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
        Schema::dropIfExists('logs_status');
    }
}
