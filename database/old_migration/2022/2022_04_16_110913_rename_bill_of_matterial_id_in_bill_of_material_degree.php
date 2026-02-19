<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBillOfMatterialIdInBillOfMaterialDegree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material_degree', function (Blueprint $table) {
            //
            $table->renameColumn("bill_of_material_id","bill_of_material_item_id");
        });

        DB::statement("ALTER TABLE `bill_of_material_degree` comment 'برای هر ردیف BOM می توان یک یا چند درجه را انتخاب نمود'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_material_degree', function (Blueprint $table) {
            //
        });
    }
}
