<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id()->startingValue(1001);
            $table->string("caption");
            $table->string("currency_id")->default(1)->comment(" | پیش فرض ریال | واحد پول");
            $table->integer("status_id")->default(520100100)->comment(" وضعیت 5201");
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
        Schema::dropIfExists('tariffs');
    }
}
