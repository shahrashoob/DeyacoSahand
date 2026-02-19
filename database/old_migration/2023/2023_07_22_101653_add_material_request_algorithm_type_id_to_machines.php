<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaterialRequestAlgorithmTypeIdToMachines extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machines', function ( Blueprint $table ) {
            //
            $table->foreignId( "raw_material_request_algorithm_type_id" )->default( 1 )->comment( "نوع الگوریتم در زمان درخواست مواد اولیه از طرف انبارک ماشین" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machines', function ( Blueprint $table ) {
            //
        } );
    }
}
