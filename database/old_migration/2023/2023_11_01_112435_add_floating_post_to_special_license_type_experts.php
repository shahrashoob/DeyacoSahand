<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFloatingPostToSpecialLicenseTypeExperts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_license_type_experts', function (Blueprint $table) {
            //
            $table->integer("floating_post")->nullable()->comment("پست های شناور، با توجه به پست ایجاد کننده مشخص می شود، 1 خود فرد، 2 مافوق سطح 1، 3 مافوق سطح 2 و ...");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special_license_type_experts', function (Blueprint $table) {
            //
        });
    }
}
