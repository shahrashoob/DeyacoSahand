<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrSizeToPackingTypeLabelPrintingTypes extends Migration
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
            $table->integer("qr_size")->default(100)->comment("انداره qr");
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
