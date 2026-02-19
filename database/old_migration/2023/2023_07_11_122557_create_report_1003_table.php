<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReport1003Table extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'report_1003', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "user_id" );
            $table->foreignId( "status_id" )->default("6030301");
            $table->longText( "data" )->comment( "اطلاعات درخواست به صورت json در این ستون ذخیره می گردد." );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'report_1003' );
    }
}
