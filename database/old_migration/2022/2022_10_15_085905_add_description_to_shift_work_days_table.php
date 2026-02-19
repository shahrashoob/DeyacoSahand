<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToShiftWorkDaysTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'shift_work_days', function ( Blueprint $table ) {
            //
            $table->string( "description" )->comment( "توضیحات روز" );
        } );
        Schema::table( 'new_shift_work_day', function ( Blueprint $table ) {
            //
            $table->string( "description" )->comment( "توضیحات روز" );
        } );
        Schema::table( 'new_shift_work_day', function ( Blueprint $table ) {
            //
            $table->rename("new_shift_work_days");
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'shift_work_days', function ( Blueprint $table ) {
            //
        } );
    }
}
