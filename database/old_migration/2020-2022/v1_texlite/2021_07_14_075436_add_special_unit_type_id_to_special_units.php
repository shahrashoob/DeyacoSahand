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
            $table->integer("special_unit_id")->default(1)->comment("گروه بندی واحد های خاص");
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
