<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumberOfPrintsToPrintPrinterFiles extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'printer_files', function ( Blueprint $table ) {
            //
            $table->integer( "number_of_prints" )->default( 1 )->comment( "تعداد پرینت هر فایل" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'print_printer_files', function ( Blueprint $table ) {
            //
        } );
    }
}
