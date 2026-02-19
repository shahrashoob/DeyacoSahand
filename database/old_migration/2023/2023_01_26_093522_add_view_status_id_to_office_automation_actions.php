<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddViewStatusIdToOfficeAutomationActions extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'office_automation_actions', function ( Blueprint $table ) {
            //
            $table->foreignId( "view_status_id" )->default(5250011)->after("status_id")->comment("وضعیت مشاهده کار توسط کاربر");
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'office_automation_works', function ( Blueprint $table ) {
            //
        } );
    }
}
