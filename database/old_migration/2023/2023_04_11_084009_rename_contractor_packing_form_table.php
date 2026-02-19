<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameContractorPackingFormTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        Schema::rename( "contractor_packing_form", "machine_allocation_packing_form" );

        Schema::table( 'machine_allocation_packing_form', function ( Blueprint $table ) {
            $table->foreignId( "machine_id" )->after("contractor_id")->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
