<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinCapacityAndMaxCapacityToProductionChannelType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_channel_types', function (Blueprint $table) {

                $table->double("min_capacity");
                $table->double("max_capacity");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_channel_type', function (Blueprint $table) {
            //
        });
    }
}
