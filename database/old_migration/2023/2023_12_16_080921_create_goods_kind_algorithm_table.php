<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindAlgorithmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_algorithm', function (Blueprint $table) {
            $table->id();
            $table->foreignId("goods_kind_id");
            $table->foreignId("algorithm_id");
            $table->foreignId("supply_type_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `goods_kind_algorithm` comment 'به ازای هر نوع تامین در رسته کالایی مشخص می کنیم که الگوریتم تخصیص و الگوریتم صدور کارت تولید به چه صورتی است.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_algorithm');
    }
}
