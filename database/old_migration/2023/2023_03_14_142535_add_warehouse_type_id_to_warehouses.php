<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseTypeIdToWarehouses extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'warehouses', function ( Blueprint $table ) {
            //
            $table->foreignId( "warehouse_type_id" )->default( 1 )->comment( "نوع انبار: انبار عادی، انبارک" );
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
