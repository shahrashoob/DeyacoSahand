<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportProductProductionTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'import_product_production', function ( Blueprint $table ) {
            $table->id();
            $table->string( "product_code" );
            $table->string( "product_id" );
            $table->string( "min_production" );
            $table->string( "max_production" );
            $table->string( "batch" );
            $table->string( "extra_production" );
            $table->string( "percent_of_extra_production" );
            $table->string( "error" );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'import_product_production' );
    }
}
