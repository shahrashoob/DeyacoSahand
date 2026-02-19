<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingTypeIdToWarehouseProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_product', function (Blueprint $table) {
            //
            $table->foreignId("packing_type_id")->after("opp_kind")->default(0)->comment("نوع بسته بندی کالا");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_product', function (Blueprint $table) {
            //
        });
    }
}
