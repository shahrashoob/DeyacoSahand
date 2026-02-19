<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportProductBaseAndStorageTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'import_product_base_and_storage', function ( Blueprint $table ) {
            $table->id();
            $table->string( "product_id" );
            $table->string( "code" );
            $table->string( "caption" );
            $table->string( "unit_id" );
            $table->string( "unit_caption" );
            $table->string( "sub_unit_id" );
            $table->string( "sub_unit_caption" );
            $table->string( "goods_type_id" );
            $table->string( "goods_kind_id" );
            $table->string( "product_type_id" );
            $table->string( "number_in_carton" );
            $table->string( "weight" );
            $table->string( "supply_type_id" );
            $table->string( "possibility_of_sale" );
            $table->string( "min_inventory" );
            $table->string( "max_inventory" );
            $table->string( "error" )->nullable();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'import_product_base_and_storage' );
    }
}
