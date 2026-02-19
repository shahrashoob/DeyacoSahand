<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindLotNumberPropertyValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_lot_number_property_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("lot_number_id");
            $table->foreignId("goods_kind_lot_number_property_id");
            $table->string("value");
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
        Schema::dropIfExists('goods_kind_lot_number_property_value');
    }
}
