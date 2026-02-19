<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataToAllocationData extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'allocation_data', function ( Blueprint $table ) {
            //
            $table->longText("data")->comment( "اطلاعاتی که در قالب json باید ذخیره شوند." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'allocation_data', function ( Blueprint $table ) {
            //
        } );
    }
}
