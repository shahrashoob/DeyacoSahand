<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBandCodeToProductionFormLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_form_logs', function (Blueprint $table) {
            //
            $table->integer("band_code")->nullable();
            $table->foreignId("production_form_item_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_form_logs', function (Blueprint $table) {
            //
        });
    }
}
