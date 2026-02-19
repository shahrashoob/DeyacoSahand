<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsernamePasswordToFinancialSoftware extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'financial_softwares', function ( Blueprint $table ) {
            //
            $table->string( "database_name" );
            $table->string( "server_url" );
            $table->string( "username" );
            $table->string( "password" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'financial_software', function ( Blueprint $table ) {
            //
        } );
    }
}
