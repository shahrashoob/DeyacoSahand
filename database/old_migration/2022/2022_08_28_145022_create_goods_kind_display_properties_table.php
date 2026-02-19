<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindDisplayPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        تکراری بود
//        Schema::create('goods_kind_display_properties', function (Blueprint $table) {
//            $table->id();
//            $table->foreignId("goods_kind_id");
//            $table->foreignId("goods_kind_property_id")->comment("در این جدول مشخص می شود هر رسته کالایی بر اساس کدام مشخصه دسته بندی می شود. (سامانه فروش) ");
//            $table->timestamps();
//        });
//        DB::statement("ALTER TABLE `goods_kind_display_properties` comment 'در این جدول مشخصه هایی که باید در لیست مشخصه های  نمایش داده شود، ذخیره می گردد'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_display_properties');
    }
}
