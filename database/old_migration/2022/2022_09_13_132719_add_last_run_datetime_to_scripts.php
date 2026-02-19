<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastRunDatetimeToScripts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'scripts', function ( Blueprint $table ) {
            //
            $table->dateTime( "last_run_datetime" )->nullable()->comment("زمان آخرین اجرای موفق اسکریپت");
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'scripts', function ( Blueprint $table ) {
            //
        } );
    }
}
