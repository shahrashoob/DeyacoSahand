<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputFormQualityControlToContractors extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'contractors', function ( Blueprint $table ) {
            //
            $table->foreignId( "input_form_quality_control_permission" )->default( 0 )->comment( "آیا فرم های ورود نیاز به تایید کنترل کیفیت دارد؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'contractors', function ( Blueprint $table ) {
            //
        } );
    }
}
