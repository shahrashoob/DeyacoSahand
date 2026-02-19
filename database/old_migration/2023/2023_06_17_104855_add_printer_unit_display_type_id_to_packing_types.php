<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrinterUnitDisplayTypeIdToPackingTypes extends Migration
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
            $table->foreignId("printer_unit_display_type_id")->default(1)->comment("نوع نمایش واحد کالا در قالب پرینتر");
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
