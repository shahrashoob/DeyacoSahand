<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsumptionCorrectionFactorToBomItem extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'bill_of_material_item', function ( Blueprint $table ) {
            //
            $table->float( "consumption_correction_factor" )->
            default( 1 )->comment( "ضریب اصلاح مصرف" );

            $table->float( "consumption_correction_factor_prediction" )->
            default( 1 )->comment( "پیش بینی ضریب اصلاح مصرف" );

            $table->float( "waste_prediction" )->
            default( 0 )->comment( "پیش بینی ضایعات" );

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'bom_item', function ( Blueprint $table ) {
            //
        } );
    }
}
