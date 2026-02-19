<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindProductFaultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_product_fault', function (Blueprint $table) {
            $table->id();
            $table->foreignId("goods_kind_id");
            $table->foreignId("product_fault_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `machine_faults` comment 'هر رسته کالایی ممکن است یک یا چند عبب کالا در آن اتقاق بیفتد'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_product_fault');
    }
}
