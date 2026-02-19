<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentMachineInputOutputBandIdToWarpsRequestFormItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warps_request_form_item', function (Blueprint $table) {
            //
            $table->
            foreignId("current_machine_input_output_band_id")->
            nullable()->
            comment("درخواست چله از این ردیف جدول ورودی های جاری ماشین زده شده");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warps_request_form_items', function (Blueprint $table) {
            //
        });
    }
}
