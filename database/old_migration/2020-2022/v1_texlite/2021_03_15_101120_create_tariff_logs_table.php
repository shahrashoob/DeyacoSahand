<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_logs', function (Blueprint $table) {
            $table->id();
            $table->integer("tariff_id");
            $table->integer("status_id");
            $table->integer("message_id")->default(0);
            $table->integer("user_id")->default(0);

            $table->string("caption");
            $table->string("currency_id")->comment(" | پیش فرض ریال | واحد پول");
            $table->dateTime("start_datetime")->comment("تاریخ شروع تعرفه");
            $table->dateTime("end_datetime")->comment("تاریخ پایان تعرفه");

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
        Schema::dropIfExists('tariff_logs');
    }
}
