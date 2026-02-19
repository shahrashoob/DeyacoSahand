<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputLineCodeFromAndInputLineCodeToToCurrentMachineInputs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
            $table->foreignId("input_line_code_to")->
            comment("کد خط ورودی از باند ورودی ( تا ورودی )")->nullable()->
            after("input_line_code");
            $table->foreignId("input_line_code_from")->
            comment("کد خط ورودی از باند ورودی ( از ورودی )")->nullable()->
            after("input_line_code");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('and_input_line_code_to_to_current_machine_inputs', function (Blueprint $table) {
            //
        });
    }
}
