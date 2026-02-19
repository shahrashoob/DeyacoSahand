<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedPercentageInLotNumberPropertyToGoodsKinds extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'goods_kinds', function ( Blueprint $table ) {
            //
            $table->float( "allowed_percentage_in_lot_number_property" )->default( 1 )->comment("درصد مجاز اختلاف بین عدد وارد شده برای  مشخصه و مقدار محاسبه شده در سیستم به ازای هر لات");
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'lot_number_property_to_goods_kinds', function ( Blueprint $table ) {
            //
        } );
    }
}
