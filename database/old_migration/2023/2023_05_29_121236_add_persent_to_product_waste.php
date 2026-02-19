<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersentToProductWaste extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_waste', function ( Blueprint $table ) {
            //
            $table->integer( "percent" )->default(0)->comment( "درصد تولید ضایعات" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_waste', function ( Blueprint $table ) {
            //
        } );
    }
}
