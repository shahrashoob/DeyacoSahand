<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountToMachineAllocationProductionChannel extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_production_channel', function ( Blueprint $table ) {
            //
            $table->double( "amount" )->comment( "مقداری از تخصیص که در این کانال تولید، تولید می شود." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation_production_channel', function ( Blueprint $table ) {
            //
        } );
    }
}
