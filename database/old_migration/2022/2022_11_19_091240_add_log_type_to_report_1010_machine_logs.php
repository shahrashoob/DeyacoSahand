<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogTypeToReport1010MachineLogs extends Migration
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
            $table->foreignId("log_type_id")->comment("1: حقیقی، 2: مجازی، 3: iot");
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
