<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierIdToLineProductStation extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'line_product_station', function ( Blueprint $table ) {
            //
            $table->foreignId( "supplier_id" )->nullable()->comment( "تامین کننده" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'line_product_station', function ( Blueprint $table ) {
            //
        } );
    }
}
