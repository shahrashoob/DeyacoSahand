<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlacedInCarrierTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carrier_types', function (Blueprint $table) {
            //
            $table->boolean("placed_in_warehouse")->comment("آیا این نوع حامل می تواند در انبار قرار گیرد");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carrier_types', function (Blueprint $table) {
            //
        });
    }
}
