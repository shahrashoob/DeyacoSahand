<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChagneFinancialSoftwareStatusIdInForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'forms', function ( Blueprint $table ) {
            //
            $table->foreignId( "financial_software_status_id" )->nullable()->default( null )->change();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'forms', function ( Blueprint $table ) {
            //
        } );
    }
}
