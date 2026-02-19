<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeOfSaleProductIdToProducts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'products', function ( Blueprint $table ) {
            //
            $table->foreignId( "type_of_sale_product_id" )->nullable()->comment( "نوع فروش کالا (فروش، کارمزدی و ...)" );
            $table->string( "service_code_in_financial_system" )->nullable()->comment( "کد خدمت در سامانه های مالی برای فروش کارمزدی" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'products', function ( Blueprint $table ) {
            //
        } );
    }
}
