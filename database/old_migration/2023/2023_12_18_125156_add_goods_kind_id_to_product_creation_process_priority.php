<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoodsKindIdToProductCreationProcessPriority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_creation_process_priority', function (Blueprint $table) {
            //
            $table->foreignId("goods_kind_id")->after("id")->comment("به ازای هر رسته کالایی فرایند طراحی کالا متفاوت است.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_creation_process_priority', function (Blueprint $table) {
            //
        });
    }
}
