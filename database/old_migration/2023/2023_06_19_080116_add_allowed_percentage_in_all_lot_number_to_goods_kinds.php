<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedPercentageInAllLotNumberToGoodsKinds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
            $table->float("allowed_percentage_in_all_lot_number")->after("allowed_percentage_in_lot_number_property")->default(10)->
            comment("درصد مجاز اختلاف بین عدد وارد شده برای وزن و مقدار محاسبه شده در همه لات ها");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('all_lot_number_to_goods_kinds', function (Blueprint $table) {
            //
        });
    }
}
