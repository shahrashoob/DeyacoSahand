<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartMachineLogIdToProductionForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
            $table->foreignId("start_machine_log_id")->comment("شمارنده های ماشین وقتی فرم تولید ایجاد می شود.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
        });
    }
}
