<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrinterTypeIdToPackingTypeLabelPrintingTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_type_label_printing_types', function (Blueprint $table) {
            //
            $table->foreignId("printer_type_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_type_label_printing_types', function (Blueprint $table) {
            //
        });
    }
}
