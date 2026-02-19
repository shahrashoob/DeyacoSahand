<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitialSecondGeneralShrinkagePercentToProductionFormItem extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'production_form_item', function ( Blueprint $table ) {
            //
            $table->float( "second_shrinkage_percent" )->nullable()->after( "shrinkage_percent" )->comment( "درصد جمع شدگی ثانویه" );
            $table->float( "general_shrinkage_percent" )->nullable()->after( "second_shrinkage_percent" )->comment( "درصد جمع شدگی کلی" );

            $table->renameColumn( "shrinkage_percent", "initial_shrinkage_percent" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'production_form_item', function ( Blueprint $table ) {
            //
        } );
    }
}
