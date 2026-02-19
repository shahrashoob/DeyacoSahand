<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductIdAndMachineAllocationIdInReport1010MachineLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_1010_machine_logs', function (Blueprint $table) {
            //
            $table->foreignId("product_id")->nullable()->change();
            $table->foreignId("machine_allocation_id")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_1010_machine_logs', function (Blueprint $table) {
            //
        });
    }
}
