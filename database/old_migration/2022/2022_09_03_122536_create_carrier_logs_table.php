<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("carrier_id");
            $table->foreignId("status_id");
            $table->foreignId("product_id")->nullable()->comment("آخرین کالایی که داخل حامل است.");
            $table->foreignId("user_id")->nullable();
            $table->foreignId("machine_id")->nullable();
            $table->foreignId("contractor_id")->nullable();
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
        Schema::dropIfExists('carrier_logs');
    }
}
