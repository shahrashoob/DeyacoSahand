<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindPropertyOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_property_options', function (Blueprint $table) {
            $table->id();
            $table->integer("goods_kind_id")->comment("جنس کالا");
            $table->integer("goods_kind_property_id")->comment("مشخصه کالا");
            $table->string("caption")->comment("عنوان ایتم لیست انتخابی");
            $table->boolean("enabled")->comment("فعال / غیر فعال")->default(true);
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
        Schema::dropIfExists('goods_kind_property_options');
    }
}
