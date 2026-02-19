<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerMessageIdToOrderLogs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'order_logs', function ( Blueprint $table ) {
            //
            $table->string( "customer_message_id" )->nullable()->comment( "پیام های قابل مشاهده برای مشتری" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'order_logs', function ( Blueprint $table ) {
            //
        } );
    }
}
