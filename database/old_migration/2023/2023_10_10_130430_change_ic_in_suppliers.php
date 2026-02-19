<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIcInSuppliers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'suppliers', function ( Blueprint $table ) {
            //
            $table->dropColumn( "ic" );
            $table->foreignId( "cost_center_id" )->comment( "مرکز هزینه" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'suppliers', function ( Blueprint $table ) {
            //
        } );
    }
}
