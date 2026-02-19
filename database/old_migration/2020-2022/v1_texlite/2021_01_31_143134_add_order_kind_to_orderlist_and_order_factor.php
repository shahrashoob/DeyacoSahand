<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderKindToOrderlistAndOrderFactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_list', function (Blueprint $table) {
            $table->integer("order_kind_id")->default(1)->comment("نوع سفارش: مشتری|تخفیف حجمی");
        });
        Schema::table('order_factor', function (Blueprint $table) {
            $table->integer("order_kind_id")->default(1)->comment("نوع سفارش: مشتری|تخفیف حجمی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orderlist_and_order_factor', function (Blueprint $table) {
            //
        });
    }
}
