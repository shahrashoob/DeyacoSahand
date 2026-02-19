<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoodsKindIdToProductCreationProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
            $table->foreignId("goods_kind_id")->nullable()->comment("گروه کالایی، در صورتیکه خدمت باشد، نال است.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
        });
    }
}
