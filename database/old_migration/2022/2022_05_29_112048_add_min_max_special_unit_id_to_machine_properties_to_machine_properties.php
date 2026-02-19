<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinMaxSpecialUnitIdToMachinePropertiesToMachineProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_properties', function (Blueprint $table) {
            //
            $table->integer("min_value")->default(0);
            $table->integer("max_value")->default(1000);
            $table->integer("field_type_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_properties_to_goods_kind_properties', function (Blueprint $table) {
            //
        });
    }
}
