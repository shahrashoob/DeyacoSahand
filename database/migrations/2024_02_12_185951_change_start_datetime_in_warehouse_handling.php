<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStartDatetimeInWarehouseHandling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_handling', function (Blueprint $table) {
            //
            $table->dateTime("start_datetime")->nullable()->change();
            $table->dateTime("end_datetime")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_handling', function (Blueprint $table) {
            //
        });
    }
}
