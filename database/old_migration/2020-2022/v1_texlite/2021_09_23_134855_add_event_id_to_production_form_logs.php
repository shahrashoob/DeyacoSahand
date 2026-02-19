<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventIdToProductionFormLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_form_logs', function (Blueprint $table) {
            //
            $table->foreignId("event_id")->comment(" رویداد در تغییر وضعیت ");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_form_logs', function (Blueprint $table) {
            //
        });
    }
}
