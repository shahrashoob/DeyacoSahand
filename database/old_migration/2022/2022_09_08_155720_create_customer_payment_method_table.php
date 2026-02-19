<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPaymentMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_payment_method', function (Blueprint $table) {
            $table->id();
            $table->foreignId("customer_id");
            $table->foreignId("payment_method_type_id")->comment("نوع روش پرداخت");
            $table->integer("percentage")->comment("حداکثر درصد مجاز پرداخت در این روش");
            $table->integer("max_check_delivery_time_in_days")->nullable()->comment("حداکثر تاریخ قابل قبول برای ثبت چک");
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
        Schema::dropIfExists('customer_payment_method_type');
    }
}
