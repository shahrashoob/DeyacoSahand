<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveStatusIdToMachineTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_types', function ( Blueprint $table ) {
            //
            $table->foreignId( "active_status_id" )->
            nullable()->comment( "وضعیت فعال بودن" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_types', function ( Blueprint $table ) {
            //
        } );
    }
}
