<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvKindCodeToFinancialSoftwareTransKind extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'financial_software_trans_kind', function ( Blueprint $table ) {
            //
            $table->integer( "inv_kind_code" )->nullable()->comment( "کد نوع برگه انبار در نرم افزار مالی (نوسا)" );
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
