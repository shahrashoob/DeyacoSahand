<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineAllocationModificationTypeIdToMachineAllocationModifications extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_modifications', function ( Blueprint $table ) {
            //
            $table->foreignId( "machine_allocation_modification_type_id" )->
            default( 1 )->
            comment( "نوع تراکنش اصلاحی: برگشت مواد اولیه یا انبار گردانی" );
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
