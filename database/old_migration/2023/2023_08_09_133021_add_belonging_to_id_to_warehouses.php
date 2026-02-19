<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBelongingToIdToWarehouses extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'warehouses', function ( Blueprint $table ) {
            //
            $table->foreignId( "belonging_to_id" )->after( "warehouse_type_id" )->nullable()->
            comment( "شناسه جدولی که انبار متعلق به آن است: ماشین، گروه ماشین، اینتگاه کاری، خط تولید و ..." );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'id_to_warehouses', function ( Blueprint $table ) {
            //
        } );
    }
}
