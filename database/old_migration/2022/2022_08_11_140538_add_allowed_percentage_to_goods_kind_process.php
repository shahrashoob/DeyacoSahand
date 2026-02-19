<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedPercentageToGoodsKindProcess extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'goods_kinds', function ( Blueprint $table ) {
            //
            $table->integer( "allowed_percentage_to_be_lower" )->default( 5 )->
            comment( "درصد مجاز کمتر بودن مقدار تحویلی در زمان تحویل کالا از طرف انبار" );
            $table->integer( "allowed_percentage_to_be_higher" )->default( 5 )->
            comment( "درصد مجاز بیشتر بودن مقدار تحویلی در زمان تحویل کالا از طرف انبار" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'goods_kind_process', function ( Blueprint $table ) {
            //
        } );
    }
}
