<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItIsCoordinationForSendingToContractors extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'contractors', function ( Blueprint $table ) {
            //
            $table->integer( "it_is_coordination_for_sending" )->
            default( 1 )->
            comment( "آیا هماهنگی دریافت مواد اولیه دارد؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'contractors', function ( Blueprint $table ) {
            //
        } );
    }
}
