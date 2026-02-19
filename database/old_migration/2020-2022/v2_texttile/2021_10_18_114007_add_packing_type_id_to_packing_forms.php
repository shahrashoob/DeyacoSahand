<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingTypeIdToPackingForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_forms', function ( Blueprint $table ) {
            //
            $table->foreignId( "packing_type_id" )->default(0)->comment( "نوع بسته بندی" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'packing_forms', function ( Blueprint $table ) {
            //
        } );
    }
}
