<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveReserveProductionIdFromMachineAllocation extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation', function ( Blueprint $table ) {
            //
            $table->dropColumn( "reserve_production_id" );
            // این ستون به جدول production_reserve انتقال پیدا کرد.
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down() {
        Schema::table( 'machine_allocation', function ( Blueprint $table ) {
            //
        } );
    }
}
