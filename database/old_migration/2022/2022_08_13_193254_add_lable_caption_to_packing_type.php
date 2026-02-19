<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLableCaptionToPackingType extends Migration
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
            $table->string("label_caption")->comment("عنوان لیبل، system=نام سامانه");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_type', function (Blueprint $table) {
            //
        });
    }
}
