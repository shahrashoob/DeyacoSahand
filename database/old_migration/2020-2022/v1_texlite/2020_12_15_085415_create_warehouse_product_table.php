<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_product', function (Blueprint $table) {
            $table->id();
            $table->integer("warehouse_id");
            $table->integer("product_id");
            $table->integer("rfw_form_id")->comment("برای دریافت  کد سفارش و کد سفارش لیست");
            $table->integer("company_id")->default(1)->comment(" شناسه شرکت | فعلا 1 ");
            $table->integer("input")->default(0)->comment(" مقدار ورودی");
            $table->integer("output")->default(0)->comment(" مقدار خروج");
            $table->integer("total_remaining")->default(0)->comment(" مانده کل ");
            $table->integer("warehouse_remaining")->default(0)->comment(" مانده انبار ");
            $table->integer("company_remaining")->default(0)->comment(" مانده شرکت ");

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
        Schema::dropIfExists('warehouse_product');
    }
}
