<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindClassificationProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_classification_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("goods_kind_classification_id");
            $table->foreignId("goods_kind_classification_option_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `goods_kind_classification_product` comment 'به ازای هر طبقه بندی در بخش رسته های کالایی ، طبقه هر کالا در هر نوع طبقه بندی در این جدول ذخیره می گردد '");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_classification_product');
    }
}
