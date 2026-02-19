<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPossibilityOfAllocationMachineToMachineStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_status', function (Blueprint $table) {
            //
            $table->boolean("possibility_of_allocation_machine")->default(false)->
                comment("آیا امکان تخصیص ماشین در این وضعیت وجود دارد؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_status', function (Blueprint $table) {
            //
        });
    }
}
