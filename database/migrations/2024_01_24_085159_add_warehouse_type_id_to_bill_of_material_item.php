<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseTypeIdToBillOfMaterialItem extends Migration
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
            $table->foreignId("warehouse_type_id")->
            after("production_status_id")->
            default(1)->
            comment("نوع انبار تحویل کالا");

            $table->foreignId("warehouse_id")->nullable()->comment("انبار تحویل کالا")->change();
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
