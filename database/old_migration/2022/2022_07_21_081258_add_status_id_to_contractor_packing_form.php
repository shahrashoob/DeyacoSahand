<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToContractorPackingForm extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'contractor_packing_form', function ( Blueprint $table ) {
            //
            $table->foreignId( "status_id" )->default( 7007006 )->comment( "وضعیت ثبت بسته تولید شده" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'contractor_packing_form', function ( Blueprint $table ) {
            //
        } );
    }
}
