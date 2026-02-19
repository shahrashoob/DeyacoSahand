<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirstPackingTypeIdToPackingTypes extends Migration
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
            $table->foreignId("first_packing_type_id")->nullable()->comment("نوع بسته بندی اولین لایه، در صورتی که لایه دوم ثبت گردید باشد.");
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
