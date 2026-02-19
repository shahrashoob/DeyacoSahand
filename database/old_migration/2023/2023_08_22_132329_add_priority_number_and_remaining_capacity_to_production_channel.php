<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityNumberAndRemainingCapacityToProductionChannel extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'production_channels', function ( Blueprint $table ) {
            //
            $table->integer( "priority_number" )->nullable()->comment( "اولویت کانال تولید" );
            $table->double( "remaining_capacity" )->nullable()->comment( "ظرفیت باقی مانده از کانال" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'produciton_channel', function ( Blueprint $table ) {
            //
        } );
    }
}
