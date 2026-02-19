<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderListImportantStatusTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'order_list_important_status', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "order_id" );
            $table->foreignId( "customer_id" );
            $table->dateTime( "event_304010_at" )->nullable()->comment( " تایید پیش نویس سفارش" );
            $table->dateTime( "event_304020_at" )->nullable()->comment( " تایید کارشناس فروش" );
            $table->dateTime( "event_304030_at" )->nullable()->comment( "تایید پیش فاکتور توسط مشتری" );
            $table->dateTime( "event_304040_at" )->nullable()->comment( "تایید کارشناس وصول مطالبات" );
            $table->dateTime( "event_304060_at" )->nullable()->comment( "تایید مدیر  مالی" );
            $table->dateTime( "event_304070_at" )->nullable()->comment( "تایید مدیر عامل" );
            $table->dateTime( "event_304075_at" )->nullable()->comment( "تایید هئیت مدیره" );
            $table->dateTime( "event_304080_at" )->nullable()->comment( "تایید پردازش " );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'order_list_important_status' );
    }
}
