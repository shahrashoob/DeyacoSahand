<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIcToWarehouses extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'warehouses', function ( Blueprint $table ) {
            //
            $table->string( "ic" )->comment( "مرکز هزینه انبار" )->default( "" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'warehouses', function ( Blueprint $table ) {
            //
        } );
    }
}
