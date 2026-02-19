<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeDegreeAmountAndWasteAmountToCurrentMachineInputs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'current_machine_inputs', function ( Blueprint $table ) {
            //
            $table->double( "waste_amount" )->nullable()->after( "actual_amount" )->comment( "مقدار تغییر درجه داده شده به ازای یک واحد کالا" );
            $table->double( "change_degree_amount" )->nullable()->after( "actual_amount" )->comment( "مقدار ضایعات به ازای یک واحد کالا" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'current_machine_inputs', function ( Blueprint $table ) {
            //
        } );
    }
}
