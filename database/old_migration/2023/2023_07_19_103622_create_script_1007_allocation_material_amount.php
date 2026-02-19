<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScript1007AllocationMaterialAmount extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'script_1007_allocation_material_amount', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "script_log_id" )->comment( "شماره اجرا شدن اسکریپت" );
            $table->foreignId( "allocation_id" );
            $table->foreignId( "material_id" );
            $table->foreignId( "priority_number" );
            $table->double( "amount_request", 15, 6 )->comment( "مقدار درخواست شده" );
            $table->double( "amount_delivered", 15, 6 )->comment( "مقدار تحویل شده" );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'script_1007_allocation_materail_amount' );
    }
}
