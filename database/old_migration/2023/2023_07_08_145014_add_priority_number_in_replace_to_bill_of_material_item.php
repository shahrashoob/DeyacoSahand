<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityNumberInReplaceToBillOfMaterialItem extends Migration
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
            $table->integer("priority_number_in_replace")->default(1)->comment("اولویت ماده اولیه در ماقبل کالاهای جایگزین");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replace_to_bill_of_material_item', function (Blueprint $table) {
            //
        });
    }
}
