<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPossibilityOfExtractionProductionFormToMachineStatus extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_status', function ( Blueprint $table ) {
            //
            $table->boolean( "possibility_of_extraction_production_form" )->default( 0 )->
            comment( "آیا امکان دارد فرم تولید ماشین در این وضعیت استخراج شود." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_status', function ( Blueprint $table ) {
            //
        } );
    }
}
