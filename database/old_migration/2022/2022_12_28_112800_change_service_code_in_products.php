<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeServiceCodeInProducts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'products', function ( Blueprint $table ) {
            //
            $table->foreignId( "service_code_in_financial_system" )->change();
            $table->renameColumn( "service_code_in_financial_system", "service_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'products', function ( Blueprint $table ) {
            //
        } );
    }
}
