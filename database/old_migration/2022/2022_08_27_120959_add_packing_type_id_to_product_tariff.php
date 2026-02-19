<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingTypeIdToProductTariff extends Migration
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
            $table->foreignId( "packing_type_id" )->comment( "نوع بسته بندی" );
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
