<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecialUnitIdToGoodsKindProperties extends Migration
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
            $table->integer("special_unit_id")->nullable()->comment("واحد اختصاصی مشخصه");
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
