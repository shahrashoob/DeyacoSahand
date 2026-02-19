<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaitingStatusIdToProductionCards extends Migration
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
            $table->foreignId("waiting_status_id")->after("status_id")->
            nullable()->comment(" وضعیت در انتظار تولید")->default(500010);
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
