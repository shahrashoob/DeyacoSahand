<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveProductCodeInContractorSystemInProducts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'products', function ( Blueprint $table ) {
            //
            $table->renameColumn( "product_code_in_contractor_system", "product_code_in_contractor_system_remove" );
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
