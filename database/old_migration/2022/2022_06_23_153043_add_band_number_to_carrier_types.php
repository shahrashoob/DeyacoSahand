<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBandNumberToCarrierTypes extends Migration
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
            $table->integer("band_number")->comment("حداکثر تعداد جایگاه برای کالا،");
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
