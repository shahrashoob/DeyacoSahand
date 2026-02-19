<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckingCompatibilityGradeInDeliveryToGoodsKinds extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'goods_kinds', function ( Blueprint $table ) {
            //
            $table->integer( "checking_compatibility_grade_in_delivery" )->
            default( 0 )->
            comment( "بررسی انطباق درجه درخواست شده کالا از انبار با درجه تحویلی کالا" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'delivery_to_goods_kinds', function ( Blueprint $table ) {
            //
        } );
    }
}
