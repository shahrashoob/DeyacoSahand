<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckDeliveryDaysToOrderPaymentMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payment_method', function (Blueprint $table) {
            //
            $table->integer("check_delivery_days")->default(0)->comment("زمان سر رسید چک ها ( روز)");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_payment_method', function (Blueprint $table) {
            //
        });
    }
}
