<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillOfMaterialItemIdToBillOfMaterialPermutationItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material_permutation_item', function (Blueprint $table) {
            //
            $table->foreignId("bill_of_material_item_id");
            $table->foreignId("bill_of_material_replace_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_material_permutation_item', function (Blueprint $table) {
            //
        });
    }
}
