<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindSettingValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_setting_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId("goods_kind_id");
            $table->foreignId("goods_kind_setting_id");
            $table->longText("value");
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
        Schema::dropIfExists('goods_kind_setting_values');
    }
}
