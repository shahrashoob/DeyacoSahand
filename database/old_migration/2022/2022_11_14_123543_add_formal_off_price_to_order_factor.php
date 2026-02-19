<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormalOffPriceToOrderFactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_factor', function (Blueprint $table) {
            //
            $table->float("tax_off_in_formal_factor_price",15,2)->default(0)->comment("مبلغ تخفیف مالیات در خرید های رسمی");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_factor', function (Blueprint $table) {
            //
        });
    }
}
