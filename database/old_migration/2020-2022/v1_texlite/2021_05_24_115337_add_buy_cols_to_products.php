<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuyColsToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            //
            $table->integer("min_buy")->nullable()->comment("حداقل خرید مواد اولیه");
            $table->integer("max_buy")->nullable()->comment("حداکثر خرید مواد اولیه");
            $table->integer("batch_buy")->nullable()->comment("بچ خرید مواد اولیه");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
