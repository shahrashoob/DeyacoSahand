<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasNumberabilityToCarrierTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'carrier_types', function ( Blueprint $table ) {
            //
            $table->integer( "has_number_ability" )->comment( "آیا نوع حامل قابلیت شماره گذاری دارد؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'carrier_types', function ( Blueprint $table ) {
            //
        } );
    }
}
