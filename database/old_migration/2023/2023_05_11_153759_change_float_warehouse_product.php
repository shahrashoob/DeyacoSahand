<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFloatWarehouseProduct extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement( "ALTER TABLE `warehouse_product` 	CHANGE COLUMN `input` `input` DOUBLE(15,7) NOT NULL DEFAULT '0'" );
        DB::statement( "ALTER TABLE `warehouse_product` 	CHANGE COLUMN `output` `output` DOUBLE(15,7) NOT NULL DEFAULT '0'" );
        DB::statement( "ALTER TABLE `warehouse_product` 	CHANGE COLUMN `sub_input` `sub_input` DOUBLE(15,7) NOT NULL DEFAULT '0'" );
        DB::statement( "ALTER TABLE `warehouse_product` 	CHANGE COLUMN `sub_output` `sub_output` DOUBLE(15,7) NOT NULL DEFAULT '0'" );


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
