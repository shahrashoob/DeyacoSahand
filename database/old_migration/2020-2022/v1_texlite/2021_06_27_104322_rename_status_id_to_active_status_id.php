<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameStatusIdToActiveStatusId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->renameColumn("status_id","active_status_id");
        });
        Schema::table('stations', function (Blueprint $table) {
            $table->renameColumn("status_id","active_status_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('active_status_id', function (Blueprint $table) {
            //
        });
    }
}
