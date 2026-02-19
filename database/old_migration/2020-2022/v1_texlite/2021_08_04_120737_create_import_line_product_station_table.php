<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportLineProductStationTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'import_line_product_station', function ( Blueprint $table ) {
            $table->id();

            $table->string( "product_code" );
            $table->string( "product_id" );
            $table->string( "line_code" );
            $table->string( "line_id" );
            $table->string( "station_code" );
            $table->string( "station_id" );

            $table->string( "machine_type_code" );
            $table->string( "machine_type_id" );
            $table->string( "min_of_production" );
            $table->string( "setup_time" );
            $table->string( "efficiency" );
            $table->string( "priority_number" );

            $table->string( "error" );


            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'import_line_product_station' );
    }
}
