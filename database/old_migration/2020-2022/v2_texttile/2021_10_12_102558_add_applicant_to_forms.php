<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicantToForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'forms', function ( Blueprint $table ) {
            //
            $table->foreignId( "applicant_type_id" )->nullable()->comment( "نوع درخواست دهنده که منجر به ایجاد فرم شده است." );
            $table->foreignId( "applicant_id" )->nullable()->comment( "شناسه عامل ایجاد کننده فرم" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'forms', function ( Blueprint $table ) {
            //
        } );
    }
}
