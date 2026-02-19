<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRealityTypeIdToPackingForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_forms', function ( Blueprint $table ) {
            //
            $table->foreignId( "reality_type_id" )->default( 1 )->comment( "نوع واقعیت: پیش فرض حقیقی" );
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
