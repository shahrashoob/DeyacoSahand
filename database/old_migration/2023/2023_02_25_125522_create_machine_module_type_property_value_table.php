<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineModuleTypePropertyValueTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'machine_module_type_property_value', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "machine_module_type_id" );
            $table->foreignId( "machine_module_type_property_id" );
            $table->float( "value" );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'machine_module_type_property_value' );
    }
}
