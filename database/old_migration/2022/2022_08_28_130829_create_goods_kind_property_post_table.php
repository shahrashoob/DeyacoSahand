<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindPropertyPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_property_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id");
            $table->foreignId("goods_kind_id");
            $table->foreignId("goods_kind_property_id");
        });

        DB::statement("ALTER TABLE `goods_kind_property_post` comment 'در این جدول مشخصه هایی که باید در لیست مشخصه های  نمایش داده شود، ذخیره می گردد'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_property_post');
    }
}
