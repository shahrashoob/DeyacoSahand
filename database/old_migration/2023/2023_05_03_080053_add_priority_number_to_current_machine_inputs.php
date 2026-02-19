<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityNumberToCurrentMachineInputs extends Migration
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
            $table->integer("priority_number")->default(0)->
            comment("اولویت انتخاب، 0 کالای اصلی، n: اولویت nام کالای اصلی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
        });
    }
}
