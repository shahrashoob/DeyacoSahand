<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDegreeIdToProductRequestFormPackingType extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_request_form_packing_type', function ( Blueprint $table ) {
            //
            $table->foreignId( "degree_id" )->default( 0 )->comment( "به ازای هر ردیف درخواست و بسته بندی، یک درجه اضافه می شود." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_request_form_packing_type', function ( Blueprint $table ) {
            //
        } );
    }
}
