<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToCarrierTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'carrier_types', function ( Blueprint $table ) {
            //
            $table->float( "average_weight" )->nullable()->comment( "میانگین وزن حامل (کیلوگرم)" )->default( 0 );
            $table->integer( "it_changes_volume_after_filling" )->default( 0 )->comment( "آیا این نوع حامل پس از تکمیل تغییر حجم دارد؟" )->default( 0 );
            $table->float( "length" )->nullable()->comment( "طول" );
            $table->float( "width" )->nullable()->comment( "عرض" );
            $table->float( "height" )->nullable()->comment( "ارتفاع" );
            $table->string( "product_code" )->nullable()->comment( "کد کالا" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'carrier_types', function ( Blueprint $table ) {
            //
        } );
    }
}
