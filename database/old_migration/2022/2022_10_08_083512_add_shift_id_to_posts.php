<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftIdToPosts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'posts', function ( Blueprint $table ) {
            //
            $table->foreignId( "shift_id" )->comment( "شیفت پست" )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'posts', function ( Blueprint $table ) {
            //
        } );
    }
}
