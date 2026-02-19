<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeptCodeAndInvSeriesToFinancialSoftwareTransKind extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'financial_software_trans_kind', function ( Blueprint $table ) {
            //
            $table->integer( "inv_series" )->nullable()->comment( "سری در تراکنش های نرم افزار مالی (نوسا)" );
            $table->integer( "dept_code" )->nullable()->comment( "کد بخش در تراکنش های نرم افزار مالی (نوسا)" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'financial_software_trans_kind', function ( Blueprint $table ) {
            //
        } );
    }
}
