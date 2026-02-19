<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingFormItemLotNumberIdToProductionFormItem extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_form_item', function ( Blueprint $table ) {
            //
            $table->foreignId( "production_form_item_lot_number_id" )->nullable()->after( "production_form_item_id" )->
            comment( "به ازای هر آیتم فرم بسته بندی مشخص است که برای کدام کالا - لات فرم تولید می باشد." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'production_form_item', function ( Blueprint $table ) {
            //
        } );
    }
}
