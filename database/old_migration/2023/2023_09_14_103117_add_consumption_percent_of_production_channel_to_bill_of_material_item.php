<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsumptionPercentOfProductionChannelToBillOfMaterialItem extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'bill_of_material_item', function ( Blueprint $table ) {
            //
            $table->float( "consumption_percent_of_production_channel" )->
            comment( "ضریف مصرف کانال تولید، به ازای یک واحد bom چقدر از کانال تولید مصرف می شود." )->
            default( 0 );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'bill_of_material_item', function ( Blueprint $table ) {
            //
        } );
    }
}
