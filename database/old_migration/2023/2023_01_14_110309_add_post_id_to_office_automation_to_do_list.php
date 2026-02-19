<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostIdToOfficeAutomationToDoList extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'office_automation_to_do_list', function ( Blueprint $table ) {
            //
            $table->foreignId( "post_id" )->after( "user_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'office_automation_to_do_list', function ( Blueprint $table ) {
            //
        } );
    }
}
