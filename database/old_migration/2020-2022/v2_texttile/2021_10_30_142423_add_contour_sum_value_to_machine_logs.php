<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContourSumValueToMachineLogs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_logs', function ( Blueprint $table ) {
            //
            $table->bigInteger( "contour_sum_value" )->default( 0 )->
            after( "machine_event_type_id" )->
            comment( "مقدار جمع کنتورها(همیشه رشد می کند" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_logs', function ( Blueprint $table ) {
            //
        } );
    }
}
