<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialLicenseTypeExpertTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_license_type_experts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("special_license_type_id")->comment("نوع مجوز");
            $table->integer("priority_number")->comment("اولویت");
            $table->foreignId("post_id")->nullable();
            $table->foreignId("committee_id")->nullable();
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
        Schema::dropIfExists('special_license_type_experts');
    }
}
