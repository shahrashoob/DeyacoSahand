<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPeopleWhoHaveADeliveryShiftNextDeliveryHavePermissionToExitToShiftDeliveryModules extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'shift_delivery_modules', function ( Blueprint $table ) {
            //
            $table->integer( "people_in_the_next_delivery_have_permission_to_exit" )->default( 1 )->
            comment( "آیا افرادی که تحویل شیفت دارند، پس از تحویل شیفت، می توانند از سازمان خارج شوند؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'shift_delivery_modules', function ( Blueprint $table ) {
            //
        } );
    }
}
