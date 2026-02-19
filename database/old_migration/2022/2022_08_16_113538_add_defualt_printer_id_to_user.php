<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefualtPrinterIdToUser extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'users', function ( Blueprint $table ) {
            //
            $table->foreignId( "default_printer_id" )->nullable()->comment( "پرینتر پیش فرض سیستم" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'user', function ( Blueprint $table ) {
            //
        } );
    }
}
