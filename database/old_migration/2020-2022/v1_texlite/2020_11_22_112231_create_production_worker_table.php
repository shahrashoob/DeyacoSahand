<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionWorkerTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'production_worker', function ( Blueprint $table ) {
            $table->id();

            $table->foreignId( "production_date_time_id" )->constrained()->comment( "رکورد زمان شیفت کاری" );
            $table->foreignId( "production_card_id" )->constrained();
            $table->foreignId( "post_id" )->constrained()->nullable();
            $table->string( "post_name" )->nullable();
            $table->foreignId( "worker_id" )->constrained();
            $table->foreignId( "status_id" )->constrained()->nullable();


            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'production_worker' );
    }
}
