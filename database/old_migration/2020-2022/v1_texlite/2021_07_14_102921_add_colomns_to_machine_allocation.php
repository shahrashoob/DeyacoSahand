<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColomnsToMachineAllocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
            $table->integer("band_code")->nullable()->comment("شماره باند ماشین");
            $table->foreignId("product_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
        });
    }
}
