<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMinOfProductionToLineProductStation extends Migration
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
            $table->renameColumn("min_of_production","practical_capacity_of_production");
        });
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->float("min_of_production")->after("max_of_production")->default(0)->comment("حداقل تولید  (ساعت)");
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
