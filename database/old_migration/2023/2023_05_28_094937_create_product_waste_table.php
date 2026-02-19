<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductWasteTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'product_waste', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "product_id" );
            $table->foreignId( "waste_id" )->comment( "کد کالای ضایعات" );
            $table->foreignId( "waste_type_id" )->comment( "نوع ضایعات" );
            $table->foreignId( "product_route_id" )->comment( "ضایعات مربوط به مسیر محصول" )->nullable();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'product_waste' );
    }
}
