<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogMessageToCarriers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'carriers', function ( Blueprint $table ) {
            //
            $table->string( "log_message" )->nullable()->comment( "توضیحات آخرین وضعیت حامل" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'carriers', function ( Blueprint $table ) {
            //
        } );
    }
}
