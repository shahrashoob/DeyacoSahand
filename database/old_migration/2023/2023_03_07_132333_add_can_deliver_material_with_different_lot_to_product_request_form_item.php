<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanDeliverMaterialWithDifferentLotToProductRequestFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
            $table->integer("can_deliver_material_with_different_lot")->comment("آیا انبار می تواند کالا را با لات های مختلف در یک برگ خروج تحویل دهد.")->
                default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
        });
    }
}
