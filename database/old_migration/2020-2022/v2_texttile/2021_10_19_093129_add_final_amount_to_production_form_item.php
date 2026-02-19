<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalAmountToProductionFormItem extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'production_form_item', function ( Blueprint $table ) {
            //
            $table->float( "final_amount" )->nullable()->
            after( "amount_after_control" )->comment( "مقدار نهایی در هنگام بسته بندی" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'production_form_item', function ( Blueprint $table ) {
            //
        } );
    }
}
