<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinPercentOfCommitteeToSpecialLicenseTypeExperts extends Migration
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
            $table->integer("min_percent_of_committee")->nullable()->after("committee_id")->comment("حداقل چند درصد کمیته باید تایید کنند تا نظر کیمته تایید شود.");
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
