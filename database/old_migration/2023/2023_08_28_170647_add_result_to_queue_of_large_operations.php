<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResultToQueueOfLargeOperations extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'queue_of_large_operations', function ( Blueprint $table ) {
            //
            $table->longText( "result" )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'queue_of_large_operations', function ( Blueprint $table ) {
            //
        } );
    }
}
