<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineProductPropertiesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'machine_product_properties', function ( Blueprint $table ) {
            $table->id();
            $table->string( "caption" );
            $table->foreignId( "station_id" );
            $table->integer( "min_value" );
            $table->integer( "max_value" );
            $table->foreignId( "field_type_id" );
            $table->foreignId( "special_unit_id" );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'machine_product_properties' );
    }
}
