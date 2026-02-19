<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelectedToProductRequestFormDelivery extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_request_form_delivery', function ( Blueprint $table ) {
            //
            $table->integer( "packing_form_selected" )->default( "0" )->comment( "آیا بسته بندی برای خروج انتخاب شده است؟" );
            $table->foreignId( "product_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_request_form_delivery', function ( Blueprint $table ) {
            //
        } );
    }
}
