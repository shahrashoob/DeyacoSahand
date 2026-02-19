<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractorIdToProductionForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'production_forms', function ( Blueprint $table ) {
            //
            $table->foreignId( "contractor_id" )->nullable()->after( "machine_id" )->comment( "فرم های تولیدی که توسط پیمانکار تکمیل می شوند، این ستون مقدار میگیرد." );
            $table->foreignId( "machine_id" )->nullable()->change();
            $table->foreignId( "carrier_id" )->nullable()->change();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'production_forms', function ( Blueprint $table ) {
            //
        } );
    }
}
