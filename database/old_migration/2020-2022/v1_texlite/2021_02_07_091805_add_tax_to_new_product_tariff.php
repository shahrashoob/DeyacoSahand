<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxToNewProductTariff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_tariff_product', function (Blueprint $table) {
            //
            //
            $table->integer("tax")->default(0)->comment("درصد مالیات");
            $table->integer("fare")->default(0)->comment("درصد عوارض");
            $table->integer("consumer_price")->default(0)->comment("قیمت مصرف کننده");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_product_tariff', function (Blueprint $table) {
            //
        });
    }
}
