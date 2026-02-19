<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCaptionAndAreaCodeToCountries extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'countries', function ( Blueprint $table ) {
            //
            $table->integer( "area_code" );
            $table->string( "caption" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'countries', function ( Blueprint $table ) {
            //
        } );
    }
}
