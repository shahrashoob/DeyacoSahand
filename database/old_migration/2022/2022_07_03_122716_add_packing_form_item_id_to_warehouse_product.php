<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingFormItemIdToWarehouseProduct extends Migration
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
            $table->foreignId("packing_form_item_id")->nullable()->comment("شناسه آیتم بسته بندی");
            $table->foreignId("last_layer_packing_form_id")->nullable()->comment("شناسه فرم بسته بندی آخرین لایه، اگر بسته بندی چند لایه داشیتم، آخرین لایه در انبار قرار می گیرد. ");
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
