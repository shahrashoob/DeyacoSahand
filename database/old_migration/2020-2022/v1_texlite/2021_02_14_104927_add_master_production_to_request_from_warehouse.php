<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterProductionToRequestFromWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_from_warehouse', function (Blueprint $table) {
            //
            $table->integer("master_production_id")->nullable()->comment("کد درخواست های گروهی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_from_warhouse', function (Blueprint $table) {
            //
        });
    }
}
