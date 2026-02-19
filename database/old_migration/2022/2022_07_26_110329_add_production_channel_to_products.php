<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionChannelToProducts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'products', function ( Blueprint $table ) {
            //
            $table->foreignId( "production_channel_id" )->nullable()->comment( "کانال تولید برای کالا" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'products', function ( Blueprint $table ) {
            //
        } );
    }
}
