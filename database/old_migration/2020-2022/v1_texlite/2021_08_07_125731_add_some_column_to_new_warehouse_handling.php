<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToNewWarehouseHandling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_warehouse_handling', function (Blueprint $table) {
            //
            $table->string("lot_number_id");
            $table->string("lot_number_code");
            $table->string("degree_id");
            $table->string("degree_code");
            $table->string("carrier_id");
            $table->string("carrier_code");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_warehouse_handling', function (Blueprint $table) {
            //
        });
    }
}
