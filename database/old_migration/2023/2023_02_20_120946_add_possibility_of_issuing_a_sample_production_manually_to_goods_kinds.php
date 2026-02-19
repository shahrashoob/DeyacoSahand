<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPossibilityOfIssuingASampleProductionManuallyToGoodsKinds extends Migration
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
            $table->integer("possibility_of_issuing_a_sample_production_manually")->default(0)->
                comment("امکان صدور برگ دستور تولید نمونه گیری به صورت دستی برای کالاهای رسته کالایی");

            // remove add in the first name
            $table->renameColumn("add_possibility_of_issuing_a_production_manually","possibility_of_issuing_a_production_manually");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
        });
    }
}
