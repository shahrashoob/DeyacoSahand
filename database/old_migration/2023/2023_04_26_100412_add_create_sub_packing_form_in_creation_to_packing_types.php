<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreateSubPackingFormInCreationToPackingTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_types', function ( Blueprint $table ) {
            //
            $table->foreignId( "create_sub_packing_form_in_creation" )->default( 0 )->
            comment( "آیا بسته بندی های فرعی در زمان ایجاد بسته بندی تعریف شوند؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'creation_to_packing_types', function ( Blueprint $table ) {
            //
        } );
    }
}
