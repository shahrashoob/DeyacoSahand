<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileIdToProductCreationProcesses extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_creation_processes', function ( Blueprint $table ) {
            //
            $table->foreignId( "file_id" )->comment( "تصویر نمونه کالا" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_creation_processes', function ( Blueprint $table ) {
            //
        } );
    }
}
