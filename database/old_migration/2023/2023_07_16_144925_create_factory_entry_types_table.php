<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactoryEntryTypesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'factory_entry_types', function ( Blueprint $table ) {
            $table->id();
            $table->string( "caption" );
            $table->timestamps();
        } );
        DB::statement("ALTER TABLE `factory_entry_types` comment 'در این جدول انواع روش هایی که یک فرم می تواند به کارخانه وارد شود ذخیره می گردد.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'factory_entry_types' );
    }
}
