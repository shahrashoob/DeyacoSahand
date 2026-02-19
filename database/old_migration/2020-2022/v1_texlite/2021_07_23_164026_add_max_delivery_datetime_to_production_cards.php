<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxDeliveryDatetimeToProductionCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_cards', function (Blueprint $table) {
            //
            $table->dateTime("max_delivery_datetime")->nullable()->comment("حداکثر تاریخ تحویل");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_cards', function (Blueprint $table) {
            //
        });
    }
}
