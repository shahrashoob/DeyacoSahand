<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToUsers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'users', function ( Blueprint $table ) {
            //
            $table->string( "father_name" )->nullable()->comment( "نام پدر" );
            $table->dateTime( "date_of_contract" )->nullable()->comment( "تاریخ قرارداد" );
            $table->foreignId( "entry_permit_status_id" )->comment( "وضعیت مجوز وورد" )->default( 461000200 );
            $table->foreignId( "exit_permit_status_id" )->comment( "وضعیت مجوز خروج" )->default( 461000200 );
            $table->foreignId( "status_id" )->comment( "وضعیت حضور کاربر در سازمان" )->default( 4620008 );
            $table->foreignId( "image_id" )->comment( "تصویر کاربر" )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'users', function ( Blueprint $table ) {
            //
        } );
    }
}
