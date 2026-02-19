<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIncceasePercentageInInformalSaleToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->integer( "increase_percentage_in_informal_sale" )->default( 0 )->
            comment( "درصد افزایش قیمت در خرید های غیررسمی" );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('informal_sale_to_orders', function (Blueprint $table) {
            //
        });
    }
}
