<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionCardAllocationAlgorithmIdToGoodsKindAlgorithm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kind_algorithm', function (Blueprint $table) {
            //
            $table->foreignId("production_card_allocation_algorithm_id")->nullable()->comment("الگوریتم تخصیص کارت تولید برای رسته کالایی - نوع تامین ");
            $table->foreignId("production_card_create_algorithm_id")->nullable()->comment("الگوریتم صدور کارت تولید برای رسته کالایی - نوع تامین ");
            $table->dropColumn("algorithm_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kind_algorithm', function (Blueprint $table) {
            //
        });
    }
}
