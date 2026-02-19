<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransKindToNewPackingFormHandling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_packing_form_handling', function (Blueprint $table) {
            //
            $table->string("trans_kind");
            $table->string("packing_form_number")->change()->comment("ممکن است ردیف بسته بندی یا کد بسته بندی باشد");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_packing_form_handling', function (Blueprint $table) {
            //
        });
    }
}
