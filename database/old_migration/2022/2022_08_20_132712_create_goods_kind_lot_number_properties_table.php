<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindLotNumberPropertiesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'goods_kind_lot_number_properties', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "goods_kind_id" );
            $table->string( "caption" );
            $table->foreignId( "field_type_id" )->comment( "نوع فیلد: عدد، رشته، ..." );
            $table->foreignId( "special_unit_id" )->nullable()->comment( "واحد اختصاصی مشخصه" );
            $table->integer( "min_value" )->nullable()->comment( "حداقل برای فیلدهای عددی" );
            $table->integer( "max_value" )->nullable()->comment( "حداکثر برای فیلدهای عددی" );
            $table->integer( "priority_number" )->default( 1 )->comment( "اولویت نمایش" );
            $table->foreignId( "status_id" )->default( 1200 );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'goods_kind_lot_number_properties' );
    }
}
