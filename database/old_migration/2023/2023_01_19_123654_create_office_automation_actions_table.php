<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeAutomationActionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'office_automation_actions', function ( Blueprint $table ) {

            $table->id();
            $table->foreignId( "office_automation_work_id" );
            $table->foreignId( "office_automation_to_do_list_id" );
            $table->foreignId( "office_automation_to_do_type_id" )->comment( "نوع کار (دستور، ابلاغ و ...)" );

            $table->foreignId( "user_id" );
            $table->foreignId( "status_id" );
            $table->timestamps();
        } );

        DB::statement("ALTER TABLE `office_automation_actions` comment 'جدول لیست اقدام ها به ازای هر ارجاع '");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'office_automation_to_do_actions' );
    }
}
