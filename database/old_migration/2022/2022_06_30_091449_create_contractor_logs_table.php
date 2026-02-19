<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("contractor_id")->nullable();
            $table->foreignId("machine_allocation_id")->nullable()->comment("آیتم تخصیص");
            $table->foreignId("production_id")->nullable();
            $table->foreignId("message_id")->nullable();
            $table->foreignId("user_id")->nullable();
            $table->foreignId("production_status_id")->nullable();
            $table->foreignId("machine_allocation_status_id")->nullable();
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
        Schema::dropIfExists('contractor_logs');
    }
}
