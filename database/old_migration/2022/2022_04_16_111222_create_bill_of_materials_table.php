<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOfMaterialsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'bill_of_materials', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "product_id" )->comment("شناسا کالا");
            $table->foreignId( "product_route_id" )->comment("مسیر تولید");
            $table->string( "code" )->comment("کد bom");
            $table->string( "caption" )->comment("نام گروه bom");
            $table->foreignId( "active_status_id" );
            $table->timestamps();
        } );
        DB::statement("ALTER TABLE `bill_of_materials` comment 'هر کالا می تواند یک یا چند BOM داشته باشد، که نام آنها در این جدول ذخیره می گردد.'");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'bill_of_materials' );
    }
}
