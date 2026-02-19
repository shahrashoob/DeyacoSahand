<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTariffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_tariff', function (Blueprint $table) {
            $table->id();
            $table->integer("tariff_id");
            $table->integer("product_id");
            $table->integer("fea")->comment("قیمت یک واحد");
            $table->integer("min_buy")->comment("حداقل  تعداد خرید");
            $table->integer("max_buy")->comment("حداکثر تعداد خرید");
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
        Schema::dropIfExists('product_tariff');
    }
}
