<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOneTimeConfirmationToForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'forms', function ( Blueprint $table ) {
            //
            $table->integer( "one_time_confirmation" )->default( 0 )->
            comment( "فرم ورود یا برگ خروج به صورت یکباره تایید شود یا خیر" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'forms', function ( Blueprint $table ) {
            //
        } );
    }
}
