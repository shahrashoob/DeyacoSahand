<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialLicenseConfirmationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_license_confirmation', function (Blueprint $table) {
            $table->id();
            $table->foreignId("special_license_id")->comment("شناسه مجوز");
            $table->foreignId("post_id")->comment("شناسه پست خبره");
            $table->foreignId("committee_id")->nullable()->comment("کمیته خبره");
            $table->foreignId("user_id")->nullable()->comment("فرد تایید کننده");
            $table->foreignId("status_id")->nullable()->comment("وضعیت تایید ");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_license_confirmation');
    }
}
