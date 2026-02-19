<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMaxCapacityFromMachineProductionChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_module_type_production_channel', function (Blueprint $table) {
            //
            $table->float("min_capacity")->comment("حداقل ظرفیت")->after("production_channel_id");
            $table->float("max_capacity")->comment("حداکثر ظرفیت")->after("production_channel_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_production_channel', function (Blueprint $table) {
            //
        });
    }
}
