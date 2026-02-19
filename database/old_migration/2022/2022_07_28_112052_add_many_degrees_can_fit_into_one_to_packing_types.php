<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManyDegreesCanFitIntoOneToPackingTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_types', function (Blueprint $table) {
            //
            $table->integer("many_degrees_can_fit_into_one")->default(1)->comment("آیا چند کالا با درجه های متفاوت میتوانند داخل بسته بندی قرار بگیرد؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_types', function (Blueprint $table) {
            //
        });
    }
}
