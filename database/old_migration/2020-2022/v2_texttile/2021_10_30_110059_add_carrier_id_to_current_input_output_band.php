<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarrierIdToCurrentInputOutputBand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('current_machine_input_output_bands', function (Blueprint $table) {
            //
            $table->foreignId("carrier_id")->nullable()->after("input_line_code")->comment("شماره حامل در حال حاضر ماشین");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('current_input_output_band', function (Blueprint $table) {
            //
        });
    }
}
