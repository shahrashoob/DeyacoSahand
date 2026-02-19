<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBandCodeToFabricRawDesignFormProduction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fabric_raw_design_form_production', function (Blueprint $table) {
            //
            $table->integer("band_code")->comment("باند ماشین");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fabric_raw_design_form_production', function (Blueprint $table) {
            //
        });
    }
}
