<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSystemCanDefineNewCarrierToCarrierTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carrier_types', function (Blueprint $table) {
            //
            $table->integer("system_can_define_new_carrier")->default(1)->comment("سیستم می تواند حامل جدید تعریف کند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carrier_types', function (Blueprint $table) {
            //
        });
    }
}
