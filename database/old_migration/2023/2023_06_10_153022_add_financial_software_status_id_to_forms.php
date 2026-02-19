<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialSoftwareStatusIdToForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'forms', function ( Blueprint $table ) {
            // اول عدم نیاز به ثبت به قبلی ها
            $table->foreignId( "financial_software_status_id" )->default( 5103100 )->comment( "وضعیت ثبت تراکنش فرم در نرم افزارهای مالی" );
        } );
        Schema::table( 'forms', function ( Blueprint $table ) {
            // در انتظار ثبت برای از این به بعد
            $table->foreignId( "financial_software_status_id" )->default( 5103200 )->change();
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
