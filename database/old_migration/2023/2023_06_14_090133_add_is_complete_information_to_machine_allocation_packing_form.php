<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCompleteInformationToMachineAllocationPackingForm extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_packing_form', function ( Blueprint $table ) {
            //
            $table->integer( "need_to_complete_information" )->default( 0 )->comment( "بسته بندی نیاز به تکمیل اطلاعات دارد؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation_packing_form', function ( Blueprint $table ) {
            //
        } );
    }
}
