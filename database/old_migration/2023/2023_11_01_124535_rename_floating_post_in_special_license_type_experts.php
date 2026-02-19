<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFloatingPostInSpecialLicenseTypeExperts extends Migration
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
            $table->renameColumn("floating_post", "floating_post_type_id");
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
