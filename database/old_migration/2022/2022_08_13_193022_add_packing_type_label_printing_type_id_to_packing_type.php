<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingTypeLabelPrintingTypeIdToPackingType extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_types', function ( Blueprint $table ) {
            //
            $table->foreignId( "packing_type_label_printing_type_id" )->default( 1 )->comment( "نوع قالب بسته بندی" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'packing_type', function ( Blueprint $table ) {
            //
        } );
    }
}
