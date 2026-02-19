<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToMachineAllocationModifications extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_modifications', function ( Blueprint $table ) {
            //
            $table->foreignId( "warehouse_id" )->after( "id" )->comment( "انباری که برگشت مواد اولیه یا انبارگردانی در آن رخ داده است." );
            $table->foreignId( "machine_id" )->nullable()->change();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation_modifications', function ( Blueprint $table ) {
            //
        } );
    }
}
