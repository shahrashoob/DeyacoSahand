<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxStopAllowedToMachineStatus extends Migration
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
            $table->integer("max_stop_allowed")->nullable()->comment(" حداکثر زمان توقف مجاز در وضعیت (دقیقه)");
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
