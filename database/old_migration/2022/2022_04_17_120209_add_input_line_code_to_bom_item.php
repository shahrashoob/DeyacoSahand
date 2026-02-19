<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputLineCodeToBomItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
            $table->integer("input_line_code")->default(1)->comment("کد پیشنهادی خط ورود از باند ورودی ماشین");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bom_item', function (Blueprint $table) {
            //
        });
    }
}
