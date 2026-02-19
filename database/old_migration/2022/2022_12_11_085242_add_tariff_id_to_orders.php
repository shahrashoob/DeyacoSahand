<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTariffIdToOrders extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'orders', function ( Blueprint $table ) {
            //
            $table->foreignId( "tariff_log_id" )->nullable()->comment( "لاگ تعرفه ای که در زمان ایجاد سفارش از آن استفاده کردیم." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'orders', function ( Blueprint $table ) {
            //
        } );
    }
}
