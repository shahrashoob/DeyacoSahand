<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmartObjectsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'smart_objects', function ( Blueprint $table ) {
            $table->id();
            $table->string( "caption" );
            $table->string( "ip" );
            $table->string( "contour" )->default( 0 )->comment( "مقدار کنتور اشیاء" );
            $table->string( "contour1" )->nullable();
            $table->string( "contour2" )->nullable();
            $table->string( "contour3" )->nullable();
            $table->string( "contour4" )->nullable();
            $table->integer("port");
            $table->string( "token" )->comment( "توکن جهت فراخوانی API" );
            $table->foreignId( "status_id" )->default( "1200" );
            $table->timestamps();
        } );
        DB::statement( "ALTER TABLE `smart_objects` comment 'لیست اشیاء (ترازو و ...)'" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'objects' );
    }
}
