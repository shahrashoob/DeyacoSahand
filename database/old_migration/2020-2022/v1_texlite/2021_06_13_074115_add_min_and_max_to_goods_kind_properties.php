<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinAndMaxToGoodsKindProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kind_properties', function (Blueprint $table) {
            //
            $table->integer("min_value")->default(0)->comment("حداقل برای فیلدهای عددی");
            $table->integer("max_value")->default(1000)->comment("حداکثر برای فیلدهای عددی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kind_properties', function (Blueprint $table) {
            //
        });
    }
}
