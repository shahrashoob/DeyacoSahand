<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillOfMaterialIdToBillOfMatreialItem extends Migration
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
            $table->foreignId("bill_of_material_id")->comment("کد گروه BOM")->nullable()->after("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_matreial_item', function (Blueprint $table) {
            //
        });
    }
}
