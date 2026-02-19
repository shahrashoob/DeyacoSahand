<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStationSubOperationIdToBillOfMaterialItem extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'bill_of_material_item', function ( Blueprint $table ) {
            //
            $table->foreignId( "station_sub_operation_id" )->after("station_operation_id")->nullable()->comment( "عملیات فرعی" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'bill_of_material_item', function ( Blueprint $table ) {
            //
        } );
    }
}
