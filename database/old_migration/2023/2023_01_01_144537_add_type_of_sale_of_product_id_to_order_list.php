<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeOfSaleOfProductIdToOrderList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_list', function (Blueprint $table) {
            //
            $table->foreignId("type_of_sale_of_product_id")->default(1)->
            comment("نوع فروش کالا (فروش عادی، فروش کارمزدی و ...");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_list', function (Blueprint $table) {
            //
        });
    }
}
