<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWeightInPackingForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
        });
        DB::statement("ALTER TABLE `packing_forms` 	CHANGE COLUMN `weight` `weight` DOUBLE(15,6) NULL");
        DB::statement("ALTER TABLE `packing_forms` 	CHANGE COLUMN `gross_weight` `gross_weight` DOUBLE(15,6) NULL");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
        });
    }
}
