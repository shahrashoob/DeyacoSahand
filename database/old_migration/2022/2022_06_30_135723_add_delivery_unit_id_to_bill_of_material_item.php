<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryUnitIdToBillOfMaterialItem extends Migration
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
            $table->foreignId("delivery_unit_id")->comment("واحد تحویل کالا");
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
