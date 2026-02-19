<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePriceToFeaInNewTariffProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_tariff_product', function (Blueprint $table) {
            //
            $table->renameColumn("price","fea");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fea_in_new_tariff_product', function (Blueprint $table) {
            //
        });
    }
}
