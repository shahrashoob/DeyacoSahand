<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeToBillOfMaterialPermutation extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'bill_of_material_permutation', function ( Blueprint $table ) {
            //
            $table->text( "code" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'bill_of_material_permutation', function ( Blueprint $table ) {
            //
        } );
    }
}
