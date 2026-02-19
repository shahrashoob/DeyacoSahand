<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInOrderingCustomerCanChooseToConsumedProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consumed_products', function (Blueprint $table) {
            //
            $table->integer("in_ordering_customer_can_choose")->default(0)->
            comment("اگر نوع فروش کالا کارمزدی بود، آیا در زمان سفارش، امکان انتخاب روش ارسال کالای مصرفی به مشتری داده شود؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consumed_products', function (Blueprint $table) {
            //
        });
    }
}
