<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFabricRawTypeOfCutIdToProductionForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
            $table->foreignId("fabric_raw_type_of_cut_for_create_form_id")->nullable()->comment("نوع استخراج(برش) پارچه در هنگام تولید فرم");
            $table->foreignId("fabric_raw_type_of_cut_for_extraction_form_id")->nullable()->comment("نوع استخراج(برش) پارچه در هنگام استخراج");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
        });
    }
}
