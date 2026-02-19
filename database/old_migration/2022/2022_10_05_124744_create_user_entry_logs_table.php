<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEntryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_entry_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("user_status_id");

            $table->foreignId("entry_register_user_id")->comment("کاربر ثبت کنند ورود")->nullable();
            $table->foreignId("exit_register_user_id")->comment("کاربر ثبت کنند خروج")->nullable();

            $table->dateTime("entry_datetime")->comment("زمان ورود")->nullable();
            $table->dateTime("exit_datetime")->comment("زمان خروج")->nullable();

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
        Schema::dropIfExists('user_entry_logs');
    }
}
