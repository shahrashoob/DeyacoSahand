<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->bigInteger("amount");
            $table->foreignId("client_transaction_type_id")->default(1)->comment("نوع تراکنش - پیش فرض: 1- افزایش شارز");
            $table->string("description");
            $table->string("driver")->nullable();
            $table->string("client_transaction_id")->nullable();
            $table->string("driver_order_id")->nullable()->comment('کد رهگیری که از طرف درگاه پرداخت ارسال می گردد.(قدیمی)');
            $table->string("track_id")->nullable()->comment('کد رهگیری که از طرف درگاه پرداخت ارسال می گردد.');
            $table->string("driver_status_id")->nullable()->comment('وضعیت درایور');
            $table->foreignId("status_id");
            $table->foreignId("payment_id_in_deyaco")->nullable()->comment('کد جدول  پرداخت در دیاکو');
            $table->foreignId("order_id_in_deyaco")->nullable()->comment('کد جدول سفارش در دیاکو');
            $table->string("random")->nullable()->comment('کد رندوم که بتوان با ان وریفای انجام داد');
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
        Schema::dropIfExists('client_payments');
    }
}
