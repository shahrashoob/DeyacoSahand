<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFeaInProductTariff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_tariff', function (Blueprint $table) {
            //
            $table->float("fea",20,13)->change();
        });

        Schema::table('product_tariff_logs', function (Blueprint $table) {
            //
            $table->float("fea",20,13)->change();
        });
        Schema::table('new_tariff_product', function (Blueprint $table) {
            //
            $table->float("fea",20,13)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_tariff', function (Blueprint $table) {
            //
        });
    }
}
