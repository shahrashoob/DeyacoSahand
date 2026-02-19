<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtractionSeparatelySettingToPackingTypes extends Migration
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
            $table->integer("it_is_possible_extract_production_form_separately")->default(0)->
            comment("آیا می توان آیتم های فرم تولید را به صورت مجزا با این بسته بندی استخراج نمود");

            $table->integer("max_amount_of_production_form_separately")->default(0)->
            comment("حداکثر مقدار آیتم های فرم تولید در زمان استخراج به صورت مجزا");


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
