<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvent7007009AtToPackingFormItemImportantStatus extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'packing_form_item_important_status', function ( Blueprint $table ) {
            //
            $table->dateTime( "event_7007009_at" )->nullable()->comment( "تاریخ ثبت نهایی تولید" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'packing_form_item_important_status', function ( Blueprint $table ) {
            //
        } );
    }
}
