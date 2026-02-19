<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingFormMasterIdToPackingForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_forms', function ( Blueprint $table ) {
            //
            $table->foreignId( "packing_form_master_id" )->nullable()->
            comment( "در صورتی که بسته بندی داخل بسته بندی دیگری باشد، شناسه بسته بندی لایه اول در اینجا درج می شود." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'packing_forms', function ( Blueprint $table ) {
            //
        } );
    }
}
