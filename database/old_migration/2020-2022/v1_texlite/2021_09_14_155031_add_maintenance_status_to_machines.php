<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaintenanceStatusToMachines extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machines', function ( Blueprint $table ) {
            //
            $table->foreignId( "maintenance_status_id" )->after( "production_status_id" )->default( 6002001 )->
            comment( "وضعیت تعمیرات و نگهداری" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machines', function ( Blueprint $table ) {
            //
        } );
    }
}
