<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractorIdToMachineAllocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
            $table->foreignId("contractor_id")->after("machine_id")->nullable()->comment("پیمانکار");
            $table->foreignId("machine_id")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
        });
    }
}
