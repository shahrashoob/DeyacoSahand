<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotNumber9ToLotNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lot_numbers', function (Blueprint $table) {
            //
            $table->bigInteger("lot_number_9_id")->after("lot_number_8_id")->nullable();
            $table->bigInteger("lot_number_10_id")->after("lot_number_9_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lot_numbers', function (Blueprint $table) {
            //
        });
    }
}
