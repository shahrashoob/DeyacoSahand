<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveShrinkagePercentFromProductionForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'production_forms', function ( Blueprint $table ) {
            //
            $table->dropColumn( "shrinkage_percent" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'production_forms', function ( Blueprint $table ) {
            //
        } );
    }
}
