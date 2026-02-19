<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJsonDataTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'json_data_lists', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "other_id" );
            $table->foreignId( "message_type_id" );
            $table->longText( "data" );
            $table->timestamps();
        } );

        DB::statement("ALTER TABLE `json_data_lists` comment 'هر جا نیاز به لاگ اطلاعات به صورت json است، در این جدول ذخیره می گردد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'json_data' );
    }
}
