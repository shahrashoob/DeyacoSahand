<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackRouteToSpecialLicenseTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_license_types', function (Blueprint $table) {
            //
            $table->string("back_route")->comment("لینک برگشت از صفحه درخواست مجوز");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special_license_types', function (Blueprint $table) {
            //
        });
    }
}
