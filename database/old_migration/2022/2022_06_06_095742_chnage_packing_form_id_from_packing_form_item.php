<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChnagePackingFormIdFromPackingFormItem extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_form_item', function ( Blueprint $table ) {
            //
            $table->foreignId( "packing_form_id" )->nullable()->change();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'packing_form_item', function ( Blueprint $table ) {
            //
        } );
    }
}
