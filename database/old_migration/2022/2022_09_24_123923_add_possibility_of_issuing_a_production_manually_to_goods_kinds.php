<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPossibilityOfIssuingAProductionManuallyToGoodsKinds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
            $table->integer("add_possibility_of_issuing_a_production_manually")->default(1)->comment("امکان صدور برگ دستور تولید به صورت دستی برای کالاهای رسته کالایی");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ggods_kinds', function (Blueprint $table) {
            //
        });
    }
}
