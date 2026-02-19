<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeightConversionRateToUnits extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'units', function ( Blueprint $table ) {
            //
            $table->double( "weight_conversion_rate" )->default( 0 )->comment( "نرخ تبدیل واحد های وزنی به kg" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'units', function ( Blueprint $table ) {
            //
        } );
    }
}
