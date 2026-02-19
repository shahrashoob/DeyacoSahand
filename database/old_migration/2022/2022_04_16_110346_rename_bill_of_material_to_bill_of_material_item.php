<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBillOfMaterialToBillOfMaterialItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material', function (Blueprint $table) {
            //
            $table->rename("bill_of_material_item");
        });

        DB::statement("ALTER TABLE `bill_of_material_item` comment 'هر BOM از یک یا چند کالا(ردیف) تشکیل می شود که ردیف ها در این جدول ذخیره می گردد.'");


        Schema::table('new_bill_of_material', function (Blueprint $table) {
            //
            $table->rename("new_bill_of_material_item");
        });

        DB::statement("ALTER TABLE `new_bill_of_material_item` comment 'این جدول جهت بارگذاری bom به صورت فایل استفاده می گردد.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
        });
    }
}
