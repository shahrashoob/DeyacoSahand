<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMessageInSmsMessageResults extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'sms_message_results', function ( Blueprint $table ) {
            //
            $table->text( "message" )->change();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'sms_message_results', function ( Blueprint $table ) {
            //
        } );
    }
}
