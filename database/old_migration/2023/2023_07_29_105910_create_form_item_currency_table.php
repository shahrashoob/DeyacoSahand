<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormItemCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_item_currency', function (Blueprint $table) {
            $table->id();
            $table->foreignId("form_id");
            $table->foreignId("form_item_id");
            $table->foreignId("product_id");
            $table->double("amount",15,7);
            $table->double("price",15,2);
            $table->double("fea",15,2);
            $table->double("total_off_price",15,2);
            $table->double("total_price",15,2);
            $table->double("tax_percent",15,2);
            $table->double("tax_price",15,2);
            $table->double("total_price_with_tax",15,2);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `form_item_currency` comment 'در این جدول به ازای هر ردیف در فرم انبار مقدار ریالی(ارزی) کالا را محاسبه می کنیم.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_item_currency');
    }
}
