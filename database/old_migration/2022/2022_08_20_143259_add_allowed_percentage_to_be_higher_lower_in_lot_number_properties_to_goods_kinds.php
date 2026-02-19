<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedPercentageToBeHigherLowerInLotNumberPropertiesToGoodsKinds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('be_higher_lower_in_lot_number_properties_to_goods_kinds', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('be_higher_lower_in_lot_number_properties_to_goods_kinds', function (Blueprint $table) {
            //
        });
    }
}
