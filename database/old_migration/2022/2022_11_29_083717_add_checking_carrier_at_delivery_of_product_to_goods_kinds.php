<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckingCarrierAtDeliveryOfProductToGoodsKinds extends Migration
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
            $table->integer("checking_carrier_at_delivery_of_product")->default(1)->
            comment(" چک نمودن کد بسته بندی / حامل در زمان تایید تحویل کالا توسط انبار");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
        });
    }
}
