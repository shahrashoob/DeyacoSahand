<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierTypeIdToSuppliers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'suppliers', function ( Blueprint $table ) {
            //
            $table->foreignId( "supplier_type_id" )->comment( "نوع تامین کننده: داخلی/خارجی" );
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
