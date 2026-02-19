<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingTypeLabelPrintingTypesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'packing_type_label_printing_types', function ( Blueprint $table ) {
            $table->id();
            $table->string( "caption" );
            $table->integer( "width" )->comment( "عرض لیبل" );
            $table->integer( "long" )->comment( "طول لیبل" );
            $table->string( "orientation" )->comment( "چرخض LP" );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'packing_type_label_printing_types' );
    }
}
