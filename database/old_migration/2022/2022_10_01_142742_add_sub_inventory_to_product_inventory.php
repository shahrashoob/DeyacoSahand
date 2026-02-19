<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubInventoryToProductInventory extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_inventory', function ( Blueprint $table ) {
            //
            $table->double( "sub_inventory", 12, 4 )->comment( "مقدار فرعی" );

            $table->renameColumn( "end_inventory", "inventory" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_inventory', function ( Blueprint $table ) {
            //
        } );
    }
}
