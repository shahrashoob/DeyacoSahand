<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveStatusIdToProductRequestForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_request_forms', function ( Blueprint $table ) {
            //
            $table->integer( "active_status_id" )->default( 7005101 )->
            comment( "وضعیت فعال/غیرفعال بودن درخواست (7005)" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_request_forms', function ( Blueprint $table ) {
            //
        } );
    }
}
