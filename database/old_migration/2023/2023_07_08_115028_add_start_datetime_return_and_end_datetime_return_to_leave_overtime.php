<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartDatetimeReturnAndEndDatetimeReturnToLeaveOvertime extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'leave_overtimes', function ( Blueprint $table ) {
            //
            $table->dateTime( "start_datetime_return" )->nullable()->comment( "تاریخ شروع برگشت در جابجایی شیفت" );
            $table->dateTime( "end_datetime_return" )->nullable()->comment( "تاریخ پایان برگشت در جابجایی شیفت" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'leave_overtime', function ( Blueprint $table ) {
            //
        } );
    }
}
