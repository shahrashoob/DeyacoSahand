<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToPackingTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_types', function ( Blueprint $table ) {
            $table->integer( "weight_error_percentage" )->default(0)->comment( "در صد خطای وزن" );
            $table->float( "length" )->nullable()->comment( "طول" );
            $table->float( "width" )->nullable()->comment( "عرض" );
            $table->float( "height" )->nullable()->comment( "ارتفاع" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'packing_types', function ( Blueprint $table ) {
            //
        } );
    }
}
