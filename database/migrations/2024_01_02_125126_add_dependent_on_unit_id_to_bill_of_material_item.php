<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDependentOnUnitIdToBillOfMaterialItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
            $table->foreignId("dependent_on_main_unit_type_id")->default(1)->
            comment("واحد مرجع کالای اصلی");
            $table->foreignId("dependent_on_material_unit_type_id")->default(1)->
            comment("واحد مرجع ماده اولیه");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
        });
    }
}
