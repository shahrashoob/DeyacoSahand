<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostPostReplaceTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create( 'post_replace', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "post_id" );
            $table->foreignId( "replace_post_id" )->comment("پست جایگزین");
        } );

        DB::statement( "ALTER TABLE `post_replace` comment 'در این جدول پست های جایگین نگهداری می شوند'" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'post_post_replace' );
    }
}
