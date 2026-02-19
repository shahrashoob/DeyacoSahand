<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentPriorityNumberToSpecialLicense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_licenses', function (Blueprint $table) {
            //
            $table->integer("current_priority_number")->default(1)->comment("اولویت در انتظار تایید");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special_licenses', function (Blueprint $table) {
            //
        });
    }
}
