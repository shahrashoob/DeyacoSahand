<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarpsRequestFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warps_request_forms', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->foreignId("machine_id");
            $table->foreignId("production_id");
            $table->foreignId("product_id")->nullable()->comment("کد چله ");

            $table->foreignId("status_id");
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
        Schema::dropIfExists('warps_request_forms');
    }
}
