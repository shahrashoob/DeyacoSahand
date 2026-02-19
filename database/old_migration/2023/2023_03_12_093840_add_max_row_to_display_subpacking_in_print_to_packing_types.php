<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxRowToDisplaySubpackingInPrintToPackingTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_types', function ( Blueprint $table ) {
            //
            $table->integer( "max_row_to_display_sub_packing_in_print" )->default( 0 )->comment( "حداکثر ردیف برای نمایش بسته بندی های فرعی در پرینت" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'display_subpacking_in_print_to_packing_types', function ( Blueprint $table ) {
            //
        } );
    }
}
