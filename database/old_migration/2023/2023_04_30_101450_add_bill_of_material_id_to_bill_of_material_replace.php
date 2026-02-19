<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillOfMaterialIdToBillOfMaterialReplace extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'bill_of_material_replace', function ( Blueprint $table ) {
            //
            $table->foreignId( "bill_of_material_id" )->after( "id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'bill_of_material_replace', function ( Blueprint $table ) {
            //
        } );
    }
}
