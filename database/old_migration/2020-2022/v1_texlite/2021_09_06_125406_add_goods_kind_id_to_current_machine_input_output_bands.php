<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoodsKindIdToCurrentMachineInputOutputBands extends Migration
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
            $table->foreignId("goods_kind_id")->comment("رسته کالایی مواد اولیه");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('current_machine_input_output_bands', function (Blueprint $table) {
            //
        });
    }
}
