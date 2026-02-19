<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecialPriorityNumberToMachineProperties extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_properties', function ( Blueprint $table ) {
            //
            $table->integer( "priority_number" )->default( 1 )->comment( "اولیویت نمایش" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_properties', function ( Blueprint $table ) {
            //
        } );
    }
}
