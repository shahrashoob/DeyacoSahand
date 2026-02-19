<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitIdAndCapacityToCarrierTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'carrier_types', function ( Blueprint $table ) {
            //
            $table->foreignId( "unit_id" )->comment( "واحد سنجش مقدار کالا بر روی حامل" );

            $table->integer("min_band_number")->after("placed_in_warehouse")->comment("حداقل تعداد باند");
            $table->integer("band_number")->comment("حداکثر تعداد باند")->change();
            $table->integer("min_band_capacity")->comment("حداقل ظرفیت باند");
            $table->integer("max_band_capacity")->comment("حداکثر ظرفیت باند");

            $table->renameColumn( "band_number", "max_band_number" );

        } );


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'carrier_types', function ( Blueprint $table ) {
            //
        } );
    }
}
