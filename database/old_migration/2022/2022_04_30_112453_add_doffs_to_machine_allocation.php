<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDoffsToMachineAllocation extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation', function ( Blueprint $table ) {
            //
            $table->integer( "max_number_of_doffs" )->default( 0 )->comment( "حداکثر تعداد داف در هر تخصیص (alfa)" );
            $table->integer( "amount_of_each_doffs" )->default( 0 )->comment( "مقدار هر داف (m_alfa)" );
            $table->integer( "number_of_doffs_done" )->default( 0 )->comment( "تعداد داف های انجام شده (beta)" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation', function ( Blueprint $table ) {
            //
        } );
    }
}
