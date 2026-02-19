<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialRulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
//        Schema::create( 'financial_rule_for_trans_kinds', function ( Blueprint $table ) {
//            $table->id();
//            $table->string( "caption" );
//            $table->foreignId( "selling_type_id" )->comment( "نوع تراکنش(فروش): رسمی، غیر رسمی" );
//            $table->foreignId("financial_account_id")->nullable()->comment("طرف حساب بدهکار");
//
//            $table->timestamps();
//        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'financial_rules' );
    }
}
