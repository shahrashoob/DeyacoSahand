<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseRequestPointToMachineTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
            $table->integer("warehouse_request_point")->comment("نقطه سفارش (درخواست) انبارک ماشین (ساعت کاری انبار)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_types', function (Blueprint $table) {
            //
        });
    }
}
