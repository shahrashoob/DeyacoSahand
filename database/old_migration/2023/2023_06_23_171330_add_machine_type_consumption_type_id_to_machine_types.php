<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineTypeConsumptionTypeIdToMachineTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_types', function ( Blueprint $table ) {
            //
            $table->foreignId( "machine_type_consumption_type_id" )->default( 1 )->comment( "روش ثبت تراکنش های مصرف برای گروه ماشین" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_types', function ( Blueprint $table ) {
            //
        } );
    }
}
