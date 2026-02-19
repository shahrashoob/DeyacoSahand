<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionColsToLineProductStation extends Migration
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
            $table->foreignId("batch")->nullable()->comment(" بچ تولید ");
            $table->foreignId("extra_production")->comment(" اضافه تولید   ");
            $table->foreignId("percent_of_extra_production")->comment("درصد اضافه تولید");
            $table->foreignId("production_channel_id")->comment("کانال تولید برای کالا");
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
