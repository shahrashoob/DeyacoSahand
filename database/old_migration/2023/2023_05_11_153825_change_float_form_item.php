<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFloatFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            DB::statement("ALTER TABLE `form_item` 	CHANGE COLUMN `amount` `amount` DOUBLE(15,7) NOT NULL DEFAULT '0'");
            DB::statement("ALTER TABLE `form_item` 	CHANGE COLUMN `sub_amount` `sub_amount` DOUBLE(15,7) NOT NULL DEFAULT '0'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
