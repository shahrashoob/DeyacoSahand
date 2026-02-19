<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChagneSmsToPostsInProductFaults extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_faults', function ( Blueprint $table ) {
            //
            $table->string( "sms_to_posts" )->nullable()->change();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'posts_in_product_faults', function ( Blueprint $table ) {
            //
        } );
    }
}
