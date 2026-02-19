<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMessageIdToProductCreationProcessLogs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_creation_process_logs', function ( Blueprint $table ) {
            //
            $table->foreignId( "message_id" )->nullable()->after( "status_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_creation_process_logs', function ( Blueprint $table ) {
            //
        } );
    }
}
