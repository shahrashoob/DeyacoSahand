<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowTransactionToFinancialSystemToTransKinds extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'trans_kinds', function ( Blueprint $table ) {
            //
            $table->integer( "allow_transaction_to_financial_system" )->default( 0 )->
            comment( "آیا مجوز ثبت تراکنش در سامانه های مالی برای این رخداد وجود دارد." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'financial_system_to_trans_kinds', function ( Blueprint $table ) {
            //
        } );
    }
}
