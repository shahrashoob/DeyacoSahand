<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionSerialAndProductionParentSerialToPackingFormItemImportantStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_form_item_important_status', function (Blueprint $table) {
            //
            $table->string("production_serial")->nullable()->comment("کارت تولید");
            $table->string("parent_production_serial")->nullable()->comment("کارت تولید سطح بالا");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_form_item_important_status', function (Blueprint $table) {
            //
        });
    }
}
