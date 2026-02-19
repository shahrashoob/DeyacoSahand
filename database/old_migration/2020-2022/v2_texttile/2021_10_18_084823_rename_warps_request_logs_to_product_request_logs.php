<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameWarpsRequestLogsToProductRequestLogs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'warps_request_form_logs', function ( Blueprint $table ) {
            $table->rename( "product_request_form_logs" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_request_logs', function ( Blueprint $table ) {
            //
        } );
    }
}
