<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCaptionEnToMaterialRequestAlgorithmTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'raw_material_request_algorithm_types', function ( Blueprint $table ) {
            //
            $table->string( "directory_namespace" )->comment( "نام کلاس الگوریتم" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'material_request_algorithm_types', function ( Blueprint $table ) {
            //
        } );
    }
}
