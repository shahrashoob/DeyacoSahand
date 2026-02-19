<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecialUnitTypeIdToSpecialUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_units', function (Blueprint $table) {
            //
            $table->foreignId("special_unit_type_id")->default(1)->comment("نوع واحد را مشخص می کند.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special_units', function (Blueprint $table) {
            //
        });
    }
}
