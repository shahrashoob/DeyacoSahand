<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDependentOnMatetialIdToBillOfMaterialItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            $table->foreignId("dependent_on_material_id")->nullable()->
            comment("آیا ردیف BOM به هیچ کدام از کالاهای BOM وابسته است (null: وابسته به کالای اصلی )");
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
