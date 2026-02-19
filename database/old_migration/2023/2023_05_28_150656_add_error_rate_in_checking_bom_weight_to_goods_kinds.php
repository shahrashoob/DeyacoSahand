<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddErrorRateInCheckingBomWeightToGoodsKinds extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'goods_kinds', function ( Blueprint $table ) {
            //
            $table->integer( "error_rate_in_checking_bom_weight" )->default( 5 )->
            comment( "درصد خطای مجاز در زمان بررسی وزن BOM و وزن کالا" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'checking_bom_weight_to_goods_kinds', function ( Blueprint $table ) {
            //
        } );
    }
}
