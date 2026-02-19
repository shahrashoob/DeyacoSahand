<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeReport1005Line extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_1005_line', function (Blueprint $table) {
            $table->foreignId("station_id")->nullable()->change();
            $table->foreignId("machine_type_id")->nullable()->change();
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
        //
    }
}
