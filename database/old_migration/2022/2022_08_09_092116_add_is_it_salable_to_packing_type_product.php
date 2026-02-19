<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsItSalableToPackingTypeProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_type_product', function (Blueprint $table) {
            //
            $table->integer("is_it_salable")->default(0)->comment("آیا این بسته بندی قابلیت فروش دارد؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_type_product', function (Blueprint $table) {
            //
        });
    }
}
