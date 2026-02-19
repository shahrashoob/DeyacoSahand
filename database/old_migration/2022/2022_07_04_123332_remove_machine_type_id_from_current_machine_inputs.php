<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMachineTypeIdFromCurrentMachineInputs extends Migration
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
            $table->dropColumn("machine_type_id");
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
