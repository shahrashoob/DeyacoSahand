<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductionChannelIdInLineProductStation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->renameColumn( "production_channel_id", "production_channel_type_id" );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
        });
    }
}
