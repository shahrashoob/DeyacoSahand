<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestWarehouseToCurrentMachineInputs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
            $table->foreignId("request_warehouse_id")->nullable()->comment("انبار درخواست کالا");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('current_machine_inputs', function (Blueprint $table) {
            //
        });
    }
}
