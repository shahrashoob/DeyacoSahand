<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeAutomationLogIdToOfficeAutomationFiles extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'office_automation_files', function ( Blueprint $table ) {
            //
            $table->foreignId( "office_automation_log_id" )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'office_automation_files', function ( Blueprint $table ) {
            //
        } );
    }
}
