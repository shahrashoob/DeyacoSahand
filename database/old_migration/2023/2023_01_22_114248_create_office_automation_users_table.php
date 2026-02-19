<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeAutomationUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'office_automation_users', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "user_id" );
            $table->foreignId( "office_automation_work_id" );
            $table->foreignId( "input_or_create" )->comment( "(2)ایجاد شده(1) / وارده" );
            $table->foreignId( "status_id" );
            $table->foreignId( "priority_id" );
            $table->timestamps();
        } );

//        \Illuminate\Support\Facades\DB::statement("ALTER TABLE office_automation_users comment `به ازای هر کار لیست افراد مرتبط با کار در این جدول ذخیر می شود.`");
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'office_automation_users' );
    }
}
