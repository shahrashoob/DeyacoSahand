<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountAfterControllToFabricRawGrading extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'fabric_raw_grading', function ( Blueprint $table ) {
            //
            $table->float( "amount_after_control" )->after( "amount" )->comment( "متراژ پس از کنترل کیفیت" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'fabric_raw_grading', function ( Blueprint $table ) {
            //
        } );
    }
}
