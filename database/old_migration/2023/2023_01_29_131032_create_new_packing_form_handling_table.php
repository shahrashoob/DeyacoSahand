<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewPackingFormHandlingTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'new_packing_form_handling', function ( Blueprint $table ) {
            $table->id();
            $table->string( "warehouse_code" );
            $table->integer( "warehouse_id" );

            $table->string( "product_code" )->nullable();
            $table->integer( "product_id" );

            $table->string( "lot_number_code" )->nullable();
            $table->integer( "lot_number_id" );

            $table->string( "degree_code" )->nullable();
            $table->integer( "degree_id" );

            $table->string( "carrier_code" )->nullable();
            $table->integer( "carrier_id" );

            $table->string( "packing_type_code" );
            $table->integer( "packing_type_id" );

            $table->integer( "packing_form_number" )->comment( "شماره ردیف بسته بندی در فایل اکسل، اگر دو ردیف یک شماره داشته باشند، به عنوان یک بسته بندی در نظر گرفته می شوند" );

            $table->integer( "packing_form_id" )->nullable();


            $table->double( "amount", 15, 10 )->default( 0 );
            $table->double( "sub_amount", 15, 10 )->default( 0 );
            $table->string( "ic" )->nullable();

            $table->integer( "opp_kind" );
            $table->integer( "status_id" )->default( 524000100 );
            $table->string( "error" );
            $table->string( "warning" );
            $table->integer( "has_error" );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'new_packing_form_handling' );
    }
}
