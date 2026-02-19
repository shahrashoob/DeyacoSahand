<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsMessageResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_message_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("messageid")->nullable();
            $table->string("message")->nullable();
            $table->integer("status")->nullable();
            $table->string("statustext")->nullable();
            $table->string("sender")->nullable();
            $table->string("receptor")->nullable();
            $table->integer("cost")->nullable();
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
        Schema::dropIfExists('sms_message_results');
    }
}
