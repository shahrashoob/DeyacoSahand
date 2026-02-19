<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumberCodeToMachines extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machines', function ( Blueprint $table ) {
            //
            $table->integer( "number_code" )->after("code")->nullable()->comment( "شماره ماشین" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machines', function ( Blueprint $table ) {
            //
        } );
    }
}
