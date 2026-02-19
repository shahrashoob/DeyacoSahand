<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReplacementLeaveOvertimeIdToLeaveOvertimes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'leave_overtimes', function ( Blueprint $table ) {
            //
            $table->foreignId( "replacement_leave_overtime_id" )->nullable()->comment( "شماره برگشت جانشینی " )->
            after( "status_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'leave_overtimes', function ( Blueprint $table ) {
            //
        } );
    }
}
