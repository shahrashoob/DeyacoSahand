<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendSmsForPostToOrderPermissionCustomer extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'order_permission_customer', function ( Blueprint $table ) {
            //
            $table->integer( "send_sms_for_post_id" )->nullable()->comment( "ارسال پیامک تغیر وضعیت سفارش به پست انتخاب شده" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'order_permission_customer', function ( Blueprint $table ) {
            //
        } );
    }
}
