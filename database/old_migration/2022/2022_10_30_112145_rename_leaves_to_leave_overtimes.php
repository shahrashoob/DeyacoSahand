<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLeavesToLeaveOvertimes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'leaves', function ( Blueprint $table ) {
            //
            $table->renameColumn("leave_type_id","leave_overtime_type_id");
            $table->rename( "leave_overtimes" );
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
