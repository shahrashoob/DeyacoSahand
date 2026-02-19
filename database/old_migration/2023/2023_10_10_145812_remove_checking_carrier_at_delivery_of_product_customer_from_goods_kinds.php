<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCheckingCarrierAtDeliveryOfProductCustomerFromGoodsKinds extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'goods_kinds', function ( Blueprint $table ) {
            //
            $table->dropColumn( "checking_carrier_at_delivery_of_product_customer" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'goods_kinds', function ( Blueprint $table ) {
            //
        } );
    }
}
