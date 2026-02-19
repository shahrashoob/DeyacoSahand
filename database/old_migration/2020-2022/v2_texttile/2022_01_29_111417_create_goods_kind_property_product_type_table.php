<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindPropertyProductTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_property_product_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId("goods_kind_id");
            $table->foreignId("goods_kind_property_id")->comment("مشخصه کالا");
            $table->foreignId("product_type_id")->comment("گروه کالا");
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
        Schema::dropIfExists('goods_kind_property_product_type');
    }
}
