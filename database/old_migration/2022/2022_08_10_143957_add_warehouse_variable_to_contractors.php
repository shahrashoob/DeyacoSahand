<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseVariableToContractors extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'contractors', function ( Blueprint $table ) {
            //
            $table->integer( "minimum_time_required_to_start_coordination" )->default( 0 )->comment( "حداقل مدت زمان لازم جهت شروع هماهنگی (ساعت)" );

            $table->time( "start_of_work_time" )->nullable()->comment("ساعت شروع کار انبار");
            $table->time( "end_of_work_time" )->nullable()->comment("ساعت پایان کار انبار");

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'contractors', function ( Blueprint $table ) {
            //
        } );
    }
}
