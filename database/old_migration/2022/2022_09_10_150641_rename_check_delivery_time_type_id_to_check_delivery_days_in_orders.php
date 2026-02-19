<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCheckDeliveryTimeTypeIdToCheckDeliveryDaysInOrders extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'orders', function ( Blueprint $table ) {
            //
            $table->dropColumn( "check_delivery_time_type_id" );
            $table->dropColumn( "head_of_check_type_id" );
            $table->integer( "check_delivery_days" )->default( 0 )->comment( "زمان سر رسید چک ها ( روز)" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'check_delivery_days_in_orders', function ( Blueprint $table ) {
            //
        } );
    }
}
