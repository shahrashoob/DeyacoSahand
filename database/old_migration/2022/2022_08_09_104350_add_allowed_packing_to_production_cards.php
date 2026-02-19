<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedPackingToProductionCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_cards', function (Blueprint $table) {
            //
            $table->foreignId("packing_type_id")->nullable()->comment("نوع بسته بندی مجاز، در صورت عدم انتخاب همه بسته بندی های مجاز کالا مجاز هستند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_cards', function (Blueprint $table) {
            //
        });
    }
}
