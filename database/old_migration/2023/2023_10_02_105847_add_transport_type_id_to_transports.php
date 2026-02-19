<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransportTypeIdToTransports extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'transports', function ( Blueprint $table ) {
            //
            $table->foreignId( "transport_type_id" )->after("id")->comment( "نوع بار: ورود | خروج" )->default( 2 );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'transports', function ( Blueprint $table ) {
            //
        } );
    }
}
