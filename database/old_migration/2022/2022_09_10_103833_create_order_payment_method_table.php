<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPaymentMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payment_method', function (Blueprint $table) {
            $table->id();
            $table->foreignId("payment_method_type_id");
            $table->foreignId("customer_id");
            $table->foreignId("order_id");
            $table->double("amount",15,2)->default(0)->comment("مبلغ  در روش پرداخت");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_payment_method');
    }
}
