<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("series");
            $table->integer("customer_id")->nullable();

            $table->datetime("order_datetime")->nullable()->comment(" تاریخ درخواست");
            
            $table->integer("exit_status_id")->nullable()->comment("وضعیت خروج از انبار");
            $table->datetime("exit_datetime")->nullable()->comment("تاریخ خروج");

            $table->integer("priority_id")->default(-100);

            $table->integer("status_id")->default(-100);

            // $table->double("total_weight",15,2)->comment("وزن کل - کیلو گرم");
            // $table->double("not_sent_weight",15,2)->comment("وزن بار ارسال نشده - کیلو گرم");

            // $table->integer("sent_weight_raito")->nullable()->comment("نسبت وزنی ارسال شده");

            // $table->double("in_warehouse_weight",15,2)->comment("وزن بخشی از بار موجود در انبار | مینیموم وزن انیار و وزن مانده");
            // $table->double("in_warehouse_weight_raito",15,2)->comment("وزن کل - کیلو گرم");
            
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
        Schema::dropIfExists('orders');
    }
}
