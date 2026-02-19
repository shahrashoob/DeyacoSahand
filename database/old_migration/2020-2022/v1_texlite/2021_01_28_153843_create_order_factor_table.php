<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFactorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_factor', function (Blueprint $table) {
            $table->id();
            $table->integer("order_id");
            $table->integer("order_list_id");
            $table->integer("product_id");
            $table->integer("customer_id");
            $table->integer("tariff_id");
            $table->integer("offer_id");
            $table->integer("offer_type_id")->default(100)->comment("پیش فرض بدون تخفیف");
            $table->integer("carton");
            $table->integer("number_in_carton");
            $table->integer("fea");
            $table->float("price",15,2)->default(0);
            $table->integer("percent_off")->default(0)->comment("میزان تخفیف درصدی");
            $table->double("percent_off_price",15,2)->default(0)->comment("مبلغ تخفیف درصدی");
            $table->integer("cash_off_percent")->default(0)->comment(" درصد تخفیف نقدی");
            $table->float("cash_off_price",15,2)->default(0)->comment(" مبلغ تخفیف نقدی");
            $table->float("special_off_price",15,2)->default(0)->comment(" مبلغ تخفیف خاص");
            $table->float("total_off_price",15,2)->default(0)->comment("مبلغ کل تخفیف");
            $table->float("total_price",15,2)->default(0)->comment("مبلغ کل ");
            $table->float("tax_percent",15,2)->default(0)->comment(" درصد مالیات  ");
            $table->float("tax_price",15,2)->default(0)->comment("مبلغ مالیات  ");
            $table->float("total_price_with_tax",15,2)->default(0)->comment("مبلغ کل با مالیات");
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
        Schema::dropIfExists('order_factor');
    }
}
