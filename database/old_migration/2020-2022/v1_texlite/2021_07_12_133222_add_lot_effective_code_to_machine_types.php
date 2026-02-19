<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotEffectiveCodeToMachineTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
            $table->string("lot_effective_code")->comment("کد موثر لات ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
        });
    }
}
