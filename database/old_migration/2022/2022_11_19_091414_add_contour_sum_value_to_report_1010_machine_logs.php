<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContourSumValueToReport1010MachineLogs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'report_1010_machine_logs', function ( Blueprint $table ) {
            //
            $table->double( "contour_sum_value" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'report_1010_machine_logs', function ( Blueprint $table ) {
            //
        } );
    }
}
