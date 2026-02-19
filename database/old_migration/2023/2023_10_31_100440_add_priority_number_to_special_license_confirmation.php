<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityNumberToSpecialLicenseConfirmation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_license_confirmation', function (Blueprint $table) {
            //
            $table->integer("priority_number")->after("user_id")->comment("اولویت تایید");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special_license_confirmation', function (Blueprint $table) {
            //
        });
    }
}
