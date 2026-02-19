<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsumedStatusToMachineAllocationModificationPackingForm extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_modification_packing_form', function ( Blueprint $table ) {
            //
            $table->foreignId( "consumed_status_id" )->comment( "وضعیت مصرف شدن بسته بندی در برگشت مواد اولیه" );

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation_modification_packing_form', function ( Blueprint $table ) {
            //
        } );
    }
}
