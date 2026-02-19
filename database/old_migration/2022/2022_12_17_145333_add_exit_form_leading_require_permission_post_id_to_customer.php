<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExitFormLeadingRequirePermissionPostIdToCustomer extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'customers', function ( Blueprint $table ) {
            //
            $table->integer( "exit_form_loading_require_permission" )->default( 1 )->
            after( "exit_form_guarding_require_permission_post_id" )->
            comment( "آیا برگ خروج نیاز به ارسال (بارگیری) دارد؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'customer', function ( Blueprint $table ) {
            //
        } );
    }
}
