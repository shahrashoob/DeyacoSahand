<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTariffProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_tariff_product', function (Blueprint $table) {
            $table->id();
            $table->integer("tariff_id");
            $table->integer("product_id");
            $table->string("product_code");
            $table->integer("price");
            $table->integer("min_buy")->comment("حداقل  تعداد خرید");
            $table->integer("max_buy")->comment("حداکثر تعداد خرید");
            $table->string("error");
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
        Schema::dropIfExists('new_tariff_product');
    }
}
