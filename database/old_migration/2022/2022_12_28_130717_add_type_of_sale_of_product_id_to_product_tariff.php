<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeOfSaleOfProductIdToProductTariff extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_tariff', function ( Blueprint $table ) {
            //
            $table->foreignId( "type_of_sale_of_product_id" )->default(1)->comment( "نوع فروش کالا" );
        } );
        Schema::table( 'new_tariff_product', function ( Blueprint $table ) {
            //
            $table->foreignId( "type_of_sale_of_product_id" )->default(1)->comment( "نوع فروش کالا" );
        } );
        Schema::table( 'product_tariff_logs', function ( Blueprint $table ) {
            //
            $table->foreignId( "type_of_sale_of_product_id" )->default(1)->comment( "نوع فروش کالا" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_tariff', function ( Blueprint $table ) {
            //
        } );
    }
}
