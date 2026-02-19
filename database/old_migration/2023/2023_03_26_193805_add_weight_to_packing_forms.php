<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeightToPackingForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
            $table->double("weight","10",4)->nullable()->comment("وزن خالس  بسته بندی");
            $table->double("gross_weight","10",4)->nullable()->comment("وزن نا خالص بسته بندی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
        });
    }
}
