<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRoutesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'product_routes', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "product_id" );
            $table->string( "caption" )->comment("نام مسیر");
            $table->string( "code" )->comment("");
            $table->timestamps();
        } );

        DB::statement( "ALTER TABLE `product_routes` comment 'در این جدول مسیرهای تولید یک کالا ذخیره می گردد، هر مسیر از یک یا چند ایستگاه کاری تشکیل می گردد.'" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'product_routes' );
    }
}
