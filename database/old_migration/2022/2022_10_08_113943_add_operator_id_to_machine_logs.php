<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperatorIdToMachineLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_logs', function (Blueprint $table) {
            //
            $table->foreignId( "operator_id" )->nullable()->comment( "شناسه اپراتور مسئول ماشین" );

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_logs', function (Blueprint $table) {
            //
        });
    }
}
